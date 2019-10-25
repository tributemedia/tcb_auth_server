<?php

namespace Drupal\tcb_auth_server;

use Drupal\Core\Entity\EntityTypeManager;

/**
 * An interface for strategy objects of Drupal\taxonomy\Entity\Term
 * type to provide a useful method of retrieving information from 
 * a taxonomy term based on the vocabulary.
 */
interface TCBTermInfoStrategyInterface {
  
  /**
   * Gets field values out of a term based on what vocabulary
   * it is in.
   * @param Drupal\taxonomy\Entity\Term $term Term to extract values from
   */
  public function getTCBTermInfo($term);
  
}