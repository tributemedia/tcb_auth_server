<?php

namespace Drupal\tcb_auth_server;

use Drupal\tcb_auth_server\FieldValueExtractorStrategyInterface;

/**
 * Concrete implementer of the FieldValueExtractorStrategyInterface. Returns
 * the term id that an entity reference field points to on a taxonomy term.
 */
class EntityFieldValueExtractorStrategy 
  implements FieldValueExtractorStrategyInterface {
    
  /**
   * Gets values from an entity reference field on a taxonomy term.
   * @param Drupal\taxonomy\Entity\Term $term The taxonomy term to get values
   * @param string $fieldName The name of the field to extract a value from
   * @return array
   */
  public function getValue($term, $fieldName) {
    
    $termArr = $term->toArray();
    $size = count($termArr[$fieldName]);
    $toReturn = [];
    
    for($i = 0; $i < $size; $i++) {
      
      $toReturn[] = $termArr[$fieldName][$i]['target_id'];
      
    }
    
    return $toReturn;
    
  }

}