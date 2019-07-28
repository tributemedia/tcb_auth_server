<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides a resource to query TCB User taxonomies
 *
 * @RestResource(
 *   id = "tcb_server_user_resource",
 *   label = @Translation("TCB User Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/user"
 *   }
 * )
 */
class TCBUserResource extends ResourceBase {
  
  /**
   * Serves responses to GET requests to the path specified on the class
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    
    // Set variables to values set in GET parameters
    $email = \Drupal::request()->query->get('email');
    $tid = \Drupal::request()->query->get('tid');
    $response = '';
    
    // Evaluate term id first, if passed in, as it is the most specific
    // method to search by, and quicker than searching by name
    if(!empty($tid)) {
      
      // Load the role by term id, if it exists
      $tcbUser = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($tid);
      
      // If the term exists, configure the response variable with the
      // user taxonomy information
      if(!empty($tcbUser)) {
        
        $tcbUser = $tcbUser->toArray();
        $response = ['name' => $tcbUser['name'][0]['value'],
                    'tid' => $tid,
                    'email' => $tcbUser['field_tcb_user_email'][0]['value'],
                    'default_role' => 
                      $tcbUser['field_tcb_user_role'][0]['target_id']];
                    
      }
      // Otherwise, inform the consumer that the term for the passed in ID
      // does not exist
      else {
        
        $response = ['error' => 'That term id does not exist'];
        
      }
      
    }
    // Search for the term by name
    else if(!empty($email)) {
      
      // Load all of the terms in the tcb_user taxonomy
      $termId = '';
      $tcbUsers =\Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree('tcb_user');
        
      // Loop through each one until the term with the name we're looking
      // for is found
      foreach($tcbUsers as $tcbUser) {
        
        $tid = $tcbUser->tid;
        $tcbUser = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($tid);
        $tcbUser = $tcbUser->toArray();
        
        if($email == $tcbUser['field_tcb_user_email'][0]['value']) {
          $termId = $tcbUser['tid'][0]['value'];
          break;
        }
        
      }
      
      // If we found the term that was requested, format the response with
      // the user information to be returned back
      if(!empty($termId)) {
        
        $response = ['name' => $tcbUser['name'][0]['value'],
                    'tid' => $tcbUser['tid'][0]['value'],
                    'email' => $tcbUser['field_tcb_user_email'][0]['value'],
                    'default_role' => 
                      $tcbUser['field_tcb_user_role'][0]['target_id']];
      
      }
      // Otherwise inform the user the email does not exist
      else {
        
        $response = ['error' => 'The user email requested does not exist.'];
        
      }
      
    }
    else {
      
      $response = ['error' => 'An email or tid parameter must be provided.'];
      
    }
    
    // This variable, to be added to the resource response, makes sure that
    // this response is not cached. Caching these responses causes the consumer
    // to receive the same information as their first API call until the 
    // server cache is cleared.
    $nocache = array(
      '#cache' => array(
        'max-age' => 0,
      ),
    );
    
    return (new ResourceResponse($response))
            ->addCacheableDependency($nocache);
  }
  
}
