<?php

namespace Drupal\tcb_auth_server;

/**
 * Interface that specifies all field validators must implement their own 
 * validation algorithm through the validate method.
 */
interface FieldValidatorInterface {
  
  /**
   * Validates a field on a taxonomy form.
   * @param string $fieldName The name of the field to be validated.
   * @param string $vocabName The name of the vocabulary to check against.
   * @param Drupal\Core\Render\Element\Form The form to be validated.
   * @param Drupal\Core\Form\FormStateInterface The changed form.
   * @return null
   */
  public function validate($fieldName, $vocabName, $form, $formState);
  
}