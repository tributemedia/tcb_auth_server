<?php

namespace Drupal\tcb_auth_server;

/**
 * Interface for strategy pattern of value extractors. Each field value
 * extractor will implement a getValue method.
 */
interface FieldValueExtractorStrategyInterface {
  
  public function getValue($term, $fieldName);
  
}