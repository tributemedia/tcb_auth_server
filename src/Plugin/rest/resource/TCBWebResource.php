<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\tcb_auth_server\TCBTermStandardInfoStrategy;
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
   * @return array 
   */
  protected function responseTermByID($tid) {
    
    $response = [];
    
    // Make sure required parameters have values
    if(empty($tid)) {
      
      \Drupal::logger('tcb_auth_server')
        ->error('tid cannot be an empty value.');
      
      return $response;
    }
    
    // Load the term
    $term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($tid);
    
    // If the term was successfully retrieved, populate the response
    // variable with the fields to be returned
    if(!empty($term)) {
      
      $termInfoExtractor = new TCBTermStandardInfoStrategy();
      $response = $termInfoExtractor->getTCBTermInfo($term);
      
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
   * @return array
   */
  protected function responseTermByName($name, $taxonomyName) {
    
    $response = [];
    
    // Make sure required parameters have values
    if(empty($name) || empty($taxonomyName)) {
      
      \Drupal::logger('tcb_auth_server')
        ->error('tid, and taxonomyName cannot be empty values.');
      
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
        $termInfoExtractor = new TCBTermStandardInfoStrategy();
        $response = $termInfoExtractor->getTCBTermInfo($termObj);
        
        break;
      }
        
    }
      
    // If we didn't find the term name we were looking for, inform the user
    if(empty($response)) {
        
      $response = ['error' => 'The name requested does not exist'];
      
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
  
}
