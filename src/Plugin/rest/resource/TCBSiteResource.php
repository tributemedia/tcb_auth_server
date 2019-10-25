<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\tcb_auth_server\Plugin\rest\resource\TCBWebResource;
use Drupal\tcb_auth_server\Plugin\rest\resource\ResponseField;

/**
 * Provides a resource to query TCB Site taxonomies
 *
 * @RestResource(
 *   id = "tcb_server_site_resource",
 *   label = @Translation("TCB Site Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/site"
 *   }
 * )
 */
class TCBSiteResource extends TCBWebResource {
  
  /**
   * Serves responses to GET requests to the path specified on the class
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    
    // Load parameters
    $tid = \Drupal::request()->query->get('tid');
    $name = \Drupal::request()->query->get('name');
    $response = '';
    
    // Check to see if the tid was passed in first, as it will be
    // the fastest operation
    if(!empty($tid)) {
      
      $response = $this->responseTermById($tid);
      
    }
    // Search for the passed in name
    else if(!empty($name)) {
      
      $response = $this->responseTermByName($name, 'tcb_site');
      
    }
    else {
      
      $response = ['error' => 'The parameter tid or name is required.'];
      
    }
    
    return $this->returnCacheFreeResponse($response);
  }
  
}
