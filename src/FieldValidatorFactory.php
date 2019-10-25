<?php

namespace Drupal\tcb_auth_server;

use Drupal\tcb_auth_server\TCBAbstractFactoryInterface;
use Drupal\tcb_auth_server\SimpleFieldValidator;

/**
 * Factory that implements the TCBAbstractFactoryInterface to create
 * FieldValidator objects based on the type passed in.
 */
class FieldValidatorFactory implements TCBAbstractFactoryInterface {
  
  /**
   * Creates a FieldValidator object that is requested via the type param.
   * @param string $type The type of FieldValidator to create.
   * @return FieldValidatorInterface
   */
  public function create($type) {
    
    $validator = null;
    
    switch($type) {
      
      case 'Simple':
        $validator = new SimpleFieldValidator();
        break;
      
    }
    
    return $validator;
    
  }
  
}