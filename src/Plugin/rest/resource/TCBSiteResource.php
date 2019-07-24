<?php

namespace Drupal\tcb_auth_server\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to query TCB Site taxonomies
 *
 * @RestResource(
 *   id = "tcb_server_site_resource",
 *   label = @Translation("TCB Site Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/site"
 *   }
 * )
 */
class TCBSiteResource extends ResourceBase {
  
  /**
   * Serves responses to GET requests to the path specified on the class
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    
    // Load parameters
    $tid = \Drupal::request()->query->get('tid');
    $name = \Drupal::request()->query->get('name');
    $response = '';
    
    // Check to see if the tid was passed in first, as it will be
    // the fastest operation
    if(!empty($tid)) {
      
      $site = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($tid);
      
      // If the TID requested was found, get its info
      if(!empty($site)) {
        
        $site = $site->toArray();
        
        $name = $site['name'][0]['value'];
        $defaultRole = \Drupal::entityTypeManager()
                    ->getStorage('taxonomy_term')
                    ->load($site['field_tcb_site_default_role'][0]['target_id']);
        $defaultRoleTid = '';
        $validDomains = [];
        
        // Populate the valid domains variable with the values from the array
        foreach($site['field_tcb_site_valid_domains'] as $domain) {
          
          $validDomains[] = $domain['value'];
          
        }
        
        // If default role was set on this site, get that role's tid
        // Otherwise return NA
        if(!empty($defaultRole)) {
        
          $defaultRoleTid = $defaultRole->tid->value;
        
        }
        else {
          
          $defaultRoleTid = 'NA';
        
        }
        
        // Format the response
        $response = ['name' => $name,
                      'tid' => $tid,
                      'default_role_tid' => $defaultRoleTid,
                      'valid_domains' => $validDomains
        ];
        
      }
      else {
        
        $response = ['error' => 'The tid requested does not exist.'];
        
      }
      
    }
    // Search for the passed in name
    else if(!empty($name)) {
      
      $sites = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree('tcb_site');
        
      // Loop over each term in the tcb_site taxonomy and try to find one
      // that has a name that matches what was passed in.
      foreach($sites as $site) {
        
        if($name == $site->name) {
          
          // If a match was found, gather the same information as above
          // NOTE: This could probably be improved to just be a function,
          // as much of this logic is the same as above
          $tid = $site->tid;
          $site = \Drupal::entityTypeManager()
                    ->getStorage('taxonomy_term')
                    ->load($tid);
          $site = $site->toArray();
          $defaultRole = \Drupal::entityTypeManager()
                  ->getStorage('taxonomy_term')
                  ->load($site['field_tcb_site_default_role'][0]['target_id']);
          $defaultRoleTid = '';
          $validDomains = [];
          
          foreach($site['field_tcb_site_valid_domains'] as $domain) {
          
            $validDomains[] = $domain['value'];
          
          }
        
          if(!empty($defaultRole)) {
          
            $defaultRoleTid = $defaultRole->tid->value;
          
          }
          else {
            
            $defaultRoleTid = 'NA';
          
          }
          
          $response = ['name' => $name,
                        'tid' => $tid,
                        'default_role_tid' => $defaultRoleTid,
                        'valid_domains' => $validDomains
          ];
          
        }
        
      }
      
      if(empty($response)) {
        
        $response = ['error' => 'That site name does not exist.'];
        
      }
      
    }
    else {
      
      $response = ['error' => 'The parameter tid or name is required.'];
      
    }
    
    // Make sure web request responses are not cached
    $nocache = array(
      '#cache' => array(
        'max-age' => 0,
      ),
    );
    
    return (new ResourceResponse($response))
            ->addCacheableDependency($nocache);
  }
  
}
