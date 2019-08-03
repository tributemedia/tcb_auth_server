<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\tcb_auth_server\Plugin\rest\resource\ResponseField;

/**
 * Parent class that provides functionality reused across all TCB Resource
 * end points.
 */
class TCBWebResource extends ResourceBase {
  
  /**
   * Returns a response variable containing the values of a taxonomy term 
   * that are requested based on the id of the term.
   * @param string $tid The id of the term to query
   * @param ResponseField[] $fields Array of fields to obtain from term object
   * @return array 
   */
  protected function responseTermByID($tid, $fields) {
    
    $response = [];
    
    // Make sure required parameters have values
    if(empty($tid) || empty($fields)) {
      
      \Drupal::logger('tcb_auth_server')
        ->error('tid and fields cannot be empty values.');
      
      return $response;
    }
    
    // Load the term
    $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($tid);
    
    // If the term was successfully retrieved, add a variable to the
    // response array for each field value requested to be retrieved
    if(!empty($term)) {
      
      foreach($fields as $field) {
        
        $response[$field->getName()] = $this->getTermValue($term, 
            $field->getFieldType(), 
            $field->getTarget());
        
      }
      
    }
    else {
      
      $response = ['error' => 'That term id does not exist'];
      
    }
    
    return $response;
    
  }
  
  /**
   * Returns a response variable containing the values of a taxonomy term 
   * that are requested based on the name of the term.
   * @param string $name The name of the term to look for
   * @param string $taxonomyName The name of the vocabulary to look through
   * @param ResponseField[] $fields Array of fields to obtain from term object
   * @return array
   */
  protected function responseTermByName($name, $taxonomyName, $fields) {
    
    $response = [];
    
    // Make sure required parameters have values
    if(empty($name) || empty($fields) || empty($taxonomyName)) {
      
      \Drupal::logger('tcb_auth_server')
        ->error('tid, taxonomyName and fields cannot be empty values.');
      
      return $response;
    }
    
    // Load all of the terms in the passed in taxonomy
    $termObj = '';
    $terms =\Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($taxonomyName);
        
    // Loop through each one until the term with the name we're looking
    // for is found
    foreach($terms as $term) {
      
      // If this term has the name we're looking for, load the term and
      // obtain its fields
      if($name == $term->name) {
        
        $termId = $term->tid;
        $termObj = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->load($termId);
        
        foreach($fields as $field) {
          
          $response[$field->getName()] = $this->getTermValue($termObj, 
            $field->getFieldType(), 
            $field->getTarget());
          
        }
        
        break;
      }
        
    }
      
    // If we didn't find the term name we were looking for, inform the user
    if(empty($response)) {
        
      $response = ['error' => 'The role name requested does not exist'];
      
    }
      
    return $response;
    
  }
  
  /**
   * Formats a response array into a ResourceResponse variable that instructs
   * Drupal not to cache the response.
   * @param array $response The array containing response values
   * @return ResourceResponse
   */
  protected function returnCacheFreeResponse($response) {
    
    if(empty($response)) {
      
      $response = [];
      
    }
    
    // This variable, to be added to the resource response, makes sure that
    // this response is not cached. Caching these responses causes the consumer
    // to receive the same information as their first API call until the 
    // server cache is cleared.
    $nocache = array(
      '#cache' => array(
        'max-age' => 0,
      ),
    );
    
    return (new ResourceResponse($response))
            ->addCacheableDependency($nocache);
    
  }
  
  /**
   * Returns a value within a term based on its type.
   * The following are valid types:
   * standard: Retrieves a value from a built-in field (non-custom).
   * field: Retrieves a value/s from a custom (user defined) field.
   * entity: Retrieves a value from an entity type reference field.
   * @param \Drupal\Core\Entity $term The term object
   * @param string $type The field type, as described above.
   * @param string $value The name of the field to get a value from.
   * @return mixed
   */
  private function getTermValue($term, $type, $value) {
    
    $toReturn = '';
    
    // Convert term to term array to make it easier to work with
    // and verify that the right type of object was passed in.
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
      case 'field':
        
        if(is_array($term[$value])) {
          
          $toReturn = [];
          
          foreach($term[$value] as $fieldValArray) {
            
            $toReturn[] = $fieldValArray['value'];
            
          }
          
        }
        else {
          
          $toReturn = $term[$value];
        
        }
        
        break;
      // Example: $tcbUser['field_tcb_user_role'][0]['target_id']
      case 'entity':
      
        $toReturn = $term[$value][0]['target_id'];
        
        break;
      
    }
    
    return $toReturn;
    
  }
  
}