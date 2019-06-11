<?php

namespace Drupal\import_json\Services;

/**
 * Class ImportJSONService.
 *
 */
class ImportJSONService {

  public $config;

  /**
   * Constructs a new ImportJSON object.
   */
  public function __construct() {
    $this->config = \Drupal::config('import_json.config');
  }

  public function triggerImportJSON($batch_bool = TRUE) {
    if ($this->config->get('bool_users_import_enabled')) {
      $this->importJSON('users', $this->config->get('users_url'), $batch_bool);
    }
    if ( $this->config->get('bool_posts_import_enabled')) {
      $this->importJSON('posts', $this->config->get('posts_url'), $batch_bool);
    }
  }

  public function importJSON($entity_type, $json_resource_url, $batch_bool) {
    // Get JSON from URL provided in config
    $url = $json_resource_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, true);

    if ($batch_bool) {
      // Define batch operation
      foreach (array_chunk($json, 10) as $result) {
        $operations[] = array('\Drupal\import_json\CreateEntities::CreateEntities', array($entity_type, $result));
      }

      // Build batch job
      $batch = array(
        'title' => t('Importing JSON now...'),
        'operations' => $operations,
        'init_message' => t('Starting import from JSON'),
        'error_message' => t('An error occurred during the JSON import'),
        'finished' => '\Drupal\import_json\CreateEntities::CreateEntitiesFinishedCallback',
      );

      batch_set($batch);
    } else {
      // Do not use batch (drush command)
      $create_entities = \Drupal\import_json\CreateEntities::CreateEntities($entity_type, $json, true);
    }
  }
}
