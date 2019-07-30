<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

class ResponseField {
  
  private $name;
  private $target;
  private $fieldType;
  
  public function __construct($name, $target, $type) {
    
    $this->name = $name;
    $this->target = $target;
    $this->fieldType = $type;
    
  }
  
  public function getName() {
    
    return $this->name;
    
  }
  
  public function getTarget() {
    
    return $this->target;
    
  }
  
  public function getFieldType() {
    
    return $this->fieldType;
    
  }
  
}