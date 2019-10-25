<?php

namespace Drupal\tcb_auth_server;

/**
 * Interface to define that all TCBFactoryTypes must have a method that
 * returns a string value of a type of factory for use by clients.
 */
interface TCBFactoryTypeInterface {
  
  /**
   * Returns a string containing the value of the type of factory this
   * object represents.
   * @return string
   */
  public function getFactoryType();
  
}