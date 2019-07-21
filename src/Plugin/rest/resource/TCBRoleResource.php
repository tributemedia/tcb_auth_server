<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

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
    $response = ['message' => 'Test'];
    return new ResourceResponse($response);
  }
  
}
