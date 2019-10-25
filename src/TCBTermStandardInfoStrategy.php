<?php

namespace Drupal\tcb_auth_server;

use Drupal\tcb_auth_server\TCBTermInfoStrategyInterface;
use Drupal\tcb_auth_server\StandardFieldValueExtractorStrategy;
use Drupal\tcb_auth_server\EntityFieldValueExtractorStrategy;
use Drupal\tcb_auth_server\CustomFieldValueExtractorStrategy;

/**
 * Concrete implementer of the TCBTermInfoStrategyInterface. This class
 * contains the standard algorithm for extracting field values for
 * web end points based on the vocabulary the term is in.
 */
class TCBTermStandardInfoStrategy implements TCBTermInfoStrategyInterface {
  
  private $stdExtractor;
  private $custExtractor;
  private $entExtractor;
  
  public function __construct() {
    
    $this->stdExtractor = new StandardFieldValueExtractorStrategy();
    $this->custExtractor = new CustomFieldValueExtractorStrategy();
    $this->entExtractor = new EntityFieldValueExtractorStrategy();
    
  }
  
  /**
   * {@inheritdoc}
   */
  public function getTCBTermInfo($term) {
    
    $termArray = $term->toArray();
    $info = [];
    
    // Set values that are included across all vocabularies (namd and tid)
    $info['name'] = $this->stdExtractor->getValue($term, 'name');
    $info['tid'] = $this->stdExtractor->getValue($term, 'tid');
    
    // Get the rest of the information based on the term vocabulary
    switch($term->getVocabularyId()) {
      
      // If the term is in tcb_role, only the permissions are needed
      case 'tcb_role':
        $info['permissions'] = $this->custExtractor->getValue($term, 
          'field_tcb_role_permissions');
        break;
      
      // If the term is in tcb_site, get the default role and list
      // of valid domains to include in the info
      case 'tcb_site':
        $info['default_role'] = $this->entExtractor->getValue($term, 
          'field_tcb_site_default_role');
        $validRoles = $this->custExtractor->getValue($term,
          'field_tcb_site_valid_roles');
        
        // Grab all valid roles
        if(!empty($validRoles)) {
          
          $info['valid_roles'] = [];
          
          foreach($validRoles as $validRole) {
            
            $info['valid_roles'][] = $this->getEmbeddedEntityInfo($validRole);
            
          }
          
        }
          
        if(!empty($info['default_role'])) {
          
          // Instead of only including the TID in the response, include all 
          // of the information that would normally be returned if the client
          // had asked for the info about the role.
          $embeddedTid = $info['default_role'][0];
          $info['default_role'] = $this
            ->getEmbeddedEntityInfo($embeddedTid);
          
        }
          
        $info['valid_domains'] = $this->custExtractor->getValue($term,
          'field_tcb_site_valid_domains');
        break;
      
      // If the tcb_user is requested, return email and user role 
      case 'tcb_user':
        $info['email'] = $this->stdExtractor->getValue($term,
          'field_tcb_user_email');
        $info['user_role'] = $this->entExtractor->getValue($term, 
          'field_tcb_user_role');
        
        if(!empty($info['user_role'])) {
          
          // Same as above case, include additional information about the 
          // user role as though the user had asked for it, instead of only
          // returning the tid of the user_role
          $embeddedTid = $info['user_role'][0];
          $info['user_role'] = $this->getEmbeddedEntityInfo($embeddedTid);
          
        }
        
        break;
      
    }
    
    return $info;
    
  }
  
  /**
   * Returns information about an embedded (referenced) entity.
   * @param string $termId The id of the term to extract values from.
   * @return array
   */
  private function getEmbeddedEntityInfo($termId) {
    
    $termObj = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->load($termId);
    
    if(!empty($termObj)) {
      
      return $this->getTCBTermInfo($termObj);
      
    }
    
    return null;
    
  }
  
}