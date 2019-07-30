<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\tcb_auth_server\Plugin\rest\resource\ResponseField;

class TCBWebResource extends ResourceBase {
  
  protected function responseTermByID($tid, $fields) {
    
    $response = [];
    if(empty($tid) || empty($fields)) {
      
      \Drupal::logger('tcb_auth_server')
        ->error('tid and fields cannot be empty values.');
      
      return $response;
    }
    
    $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($tid);
            
    if(!empty($term)) {
      
      foreach($fields as $field) {
        
        $response[$field->getName()] = $this->getTermValue($term, 
            $field->getFieldType(), 
            $field->getTarget());
        
      }
      
    }
    else {
      
      $response[] = ['error' => 'That term id does not exist'];
      
    }
    
    return $response;
    
  }
  
  protected function responseTermByName($name, $fields) {
    
    
    
  }
  
  private function getTermValue($term, $type, $value) {
    
    $toReturn = '';
    
    try {
      
      $term = $term->toArray();
      
    }
    catch(Exception $e) {
      
      \Drupal::logger('tcb_auth_server')
        ->error('Called toArray on non-term object.');
      
    }
    
    switch($type) {
      
      // Example: $term['name'][0]['value']
      case 'standard':
        $toReturn = $term[$value][0]['value'];
        break;
      // Example: $role['field_tcb_role_permissions']
      // Will need to be improved for fields that have multiple values
      case 'field':
        $toReturn = $term[$value];
        break;
      // Example: $tcbUser['field_tcb_user_role'][0]['target_id']
      case 'entity':
        $toReturn = $term[$value][0]['target_id'];
        break;
      
    }
    
    return $toReturn;
    
  }
  
}