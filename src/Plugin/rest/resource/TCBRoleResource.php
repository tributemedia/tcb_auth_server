<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to query TCB Role taxonomies
 *
 * @RestResource(
 *   id = "tcb_server_role_resource",
 *   label = @Translation("TCB Role Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/role"
 *   }
 * )
 */
class TCBRoleResource extends ResourceBase {
  
  /**
   * Serves responses to GET requests to the path specified on the class
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    
    // Set variables to values set in GET parameters
    $name = \Drupal::request()->query->get('name');
    $tid = \Drupal::request()->query->get('tid');
    $response = '';
    
    // Evaluate term id first, if passed in, as it is the most specific
    // method to search by, and quicker than searching by name
    if(!empty($tid)) {
      
      // Load the role by term id, if it exists
      $role = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($tid);
      
      // If the term exists, configure the response variable with the
      // role taxonomy information
      if(!empty($role)) {
        
        $role = $role->toArray();
        $response = ['name' => $role['name'][0]['value'],
                    'tid' => $tid,
                    'permissions' => $role['field_tcb_role_permissions']];
                    
      }
      // Otherwise, inform the consumer that the term for the passed in ID
      // does not exist
      else {
        
        $response = ['error' => 'That term id does not exist'];
        
      }
      
    }
    // Search for the term by name
    else if(!empty($name)) {
      
      // Load all of the terms in the tcb_role taxonomy
      $termId = '';
      $termPerms = '';
      $roles =\Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree('tcb_role');
        
      // Loop through each one until the term with the name we're looking
      // for is found
      foreach($roles as $role) {
        
        if($name == $role->name) {
          $termId = $role->tid;
          $termPerms = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($termId)
            ->toArray()['field_tcb_role_permissions'];
          break;
        }
        
      }
      
      // If we found the term that was requested, format the response with
      // the role information to be returned back
      if(!empty($termId)) {
        
        $response = ['name' => $name,
                    'tid' => $termId,
                    'permissions' => $termPerms];
      
      }
      // Otherwise inform the user the role name does not exist
      else {
        
        $response = ['error' => 'The role name requested does not exist.'];
        
      }
      
    }
    else {
      
      $response = ['error' => 'A name or tid parameter must be provided.'];
      
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
