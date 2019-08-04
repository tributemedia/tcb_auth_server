<?php

namespace Drupal\tcb_auth_server;

use Drupal\tcb_auth_server\FieldValidatorInterface;

/**
 * A validator for making sure the value of a field is unique amongst
 * a vocabulary. This class is only meant to check against fields included
 * with core, or single value non-reference type custom fields.
 */
class SimpleFieldValidator implements FieldValidatorInterface {
  
  /**
   * Validates a field on a taxonomy form to make sure it's unique This
   * SimpleFieldValidator is only meant to check against fields included
   * with core, or single value non-reference type custom fields.
   * @param string $fieldName The name of the field to be validated.
   * @param string $vocabName The name of the vocabulary to check against.
   * @param Drupal\Core\Render\Element\Form The form to be validated.
   * @param Drupal\Core\Form\FormStateInterface The changed form.
   * @return null
   */
  public function validate($fieldName, $vocabName, $form, $formState) {
    
    $tid = $form['tid']['#value'];
    $newFieldVal = $formState->getValue($fieldName)[0]['value'];
    $existingTerm = '';
    
    // If we're editing a form for an already existing term, grab the 
    // old term
    if(!empty($tid)) {
      $existingTerm = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($tid);
    }
    
    // Load all values within the vocabulary
    $vocabTerms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree($vocabName);
    
    // Loop through each term in the vocabulary and check to see if the
    // new value of the field passed in is equal to any of the other terms
    foreach($vocabTerms as $term) {
      
      $tempTerm = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($term->tid);
      $tempTerm = $tempTerm->toArray();      
      
      // If the new value of the field is equal to the value of another
      // term's value for the same value, AND if that term is not the term
      // we're editing (assuming we're editing an existing term, this check
      // will not matter if we're adding a new term) then fail the validation
      // and inform the user that they're attempting to put in a value
      // for this field that already exists in another term.
      if($tempTerm[$fieldName][0]['value'] == $newFieldVal && 
        $tempTerm['tid'][0]['value'] != $tid) {
        
        $formState->setErrorByName($fieldName,
          'There is another term with that value already.');
        
        break;
        
      }
      
    }
    
  }
  
}