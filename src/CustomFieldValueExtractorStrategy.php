<?php

namespace Drupal\tcb_auth_server;

use Drupal\tcb_auth_server\FieldValueExtractorStrategyInterface;

/**
 * Concrete implementer of the FieldValueExtractorStrategyInterface to 
 * extract the value from a custom field added to a taxonomy term.
 */
class CustomFieldValueExtractorStrategy 
  implements FieldValueExtractorStrategyInterface {
    
  /**
   * Gets value from a custom field on a taxonomy term.
   * @param Drupal\taxonomy\Entity\Term $term The taxonomy term to get values
   * @param string $fieldName The name of the field to extract a value from
   * @return mixed
   */
  public function getValue($term, $fieldName) {
    
    $term = $term->toArray();
    $toReturn = [];
    
    // If there are multiple values in the field, extract all of them into
    // an array. Otherwise, get the single value.
    if(is_array($term[$fieldName])) {
          
      foreach($term[$fieldName] as $fieldValArray) {
            
        if(!empty($fieldValArray['value'])) {
        
          $toReturn[] = $fieldValArray['value'];
          
        }
        else {
          
          $toReturn[] = $fieldValArray['target_id'];
          
        }
            
      }
          
    }
    else {
          
      $toReturn = $term[$fieldName];
        
    }
    
    return $toReturn;
    
  }

}