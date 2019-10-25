<?php

namespace Drupal\tcb_auth_server;

/**
 * Interface that defines all factories must implement a create method
 * to return some type of object based on a type passed in.
 */
interface TCBAbstractFactoryInterface {
  
  /**
   * Creates an object based on a passed in type.
   * @param mixed $type The type of object to create
   * @return mixed
   */
  public function create($type);
  
}