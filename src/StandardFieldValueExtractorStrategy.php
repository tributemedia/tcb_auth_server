<?php

namespace Drupal\tcb_auth_server;

use Drupal\tcb_auth_server\FieldValueExtractorStrategyInterface;

/**
 * Concrete implementer of the FieldValueExtractorStrategyInterface. Gets
 * a value from a standard, non-custom field on a taxonomy term.
 */
class StandardFieldValueExtractorStrategy 
  implements FieldValueExtractorStrategyInterface {
    
  /**
   * Gets value from a standard field (non-custom) on a taxonomy term.
   * @param Drupal\taxonomy\Entity\Term $term The taxonomy term to get values
   * @param string $fieldName The name of the field to extract a value from
   * @return string
   */
  public function getValue($term, $fieldName) {
    
    $term = $term->toArray();
    return $term[$fieldName][0]['value'];
    
  }

}