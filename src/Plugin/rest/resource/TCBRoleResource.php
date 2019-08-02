<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\tcb_auth_server\Plugin\rest\resource\TCBWebResource;
use Drupal\tcb_auth_server\Plugin\rest\resource\ResponseField;

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
class TCBRoleResource extends TCBWebResource {
  
  /**
   * Serves responses to GET requests to the path specified on the class
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    
    // Set variables to values set in GET parameters
    $name = \Drupal::request()->query->get('name');
    $tid = \Drupal::request()->query->get('tid');
    $response = '';
    $getTermArgs = [ 
          new ResponseField('name', 'name', 'standard'),
          new ResponseField('tid', 'tid', 'standard'),
          new ResponseField('permissions', 
           'field_tcb_role_permissions', 
           'field'),
    ];
    
    // Evaluate term id first, if passed in, as it is the most specific
    // method to search by, and quicker than searching by name
    if(!empty($tid)) {
      
      $response = $this->responseTermByID($tid, $getTermArgs);
      
    }
    // Search for the term by name
    else if(!empty($name)) {
      
      $response = $this->responseTermByName($name, 'tcb_role', $getTermArgs);
      
    }
    else {
      
      $response = ['error' => 'A name or tid parameter must be provided.'];
      
    }
    
    return $this->returnCacheFreeResponse($response);
    
  }
  
}
