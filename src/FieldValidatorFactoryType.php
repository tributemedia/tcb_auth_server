<?php

namespace Drupal\tcb_auth_server;

use Drupal\tcb_auth_server\TCBFactoryTypeInterface;

/**
 * Class that implements the TCBFactoryTypeInterface to represent a 
 * FieldValidator type factory.
 */
class FieldValidatorFactoryType implements TCBFactoryTypeInterface {
  
  /**
   * {@inheritdoc}
   */
  public function getFactoryType() {
    
    return 'FieldValidator';
    
  }
  
}