<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\tcb_auth_server\Plugin\rest\resource\TCBWebResource;
use Drupal\tcb_auth_server\Plugin\rest\resource\ResponseField;
use Drupal\tcb_auth_server\TCBTermStandardInfoStrategy;

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
class TCBUserResource extends TCBWebResource {
  
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
      
      $response = $this->responseTermById($tid);
      
    }
    // Search for the term by email
    // Special logic is required for this, so this is not using the
    // inherited search functionality from TCBWebResource
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
        
        $termObj = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($termId);
        $termInfoExtractor = new TCBTermStandardInfoStrategy();
        $response = $termInfoExtractor->getTCBTermInfo($termObj);
      
      }
      // Otherwise inform the user the email does not exist
      else {
        
        $response = ['error' => 'The user email requested does not exist.'];
        
      }
      
    }
    else {
      
      $response = ['error' => 'An email or tid parameter must be provided.'];
      
    }
    
    return $this->returnCacheFreeResponse($response);
    
  }
  
}
