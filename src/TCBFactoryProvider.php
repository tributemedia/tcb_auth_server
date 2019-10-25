<?php

namespace Drupal\tcb_auth_server;

use Drupal\tcb_auth_server\TCBFactoryTypeInterface;
use Drupal\tcb_auth_server\FieldValidatorFactory;

/**
 * Provides TCB factories to clients
 */
class TCBFactoryProvider {
  
  /**
   * Get a TCB factory based on the type passed in.
   * @param TCBFactoryTypeInterface $type The type of factory to return
   * @return TCBAbstractFactoryInterface
   */
  public static function getFactory(TCBFactoryTypeInterface $type) {
    
    $factory = null;
    
    switch($type->getFactoryType()) {
      
      case 'FieldValidator':
        $factory = new FieldValidatorFactory();
        break;
        
    }
    
    return $factory;
    
  }
  
}