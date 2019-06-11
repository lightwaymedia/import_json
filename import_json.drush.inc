<?php

/**
 * @file Contains the code to generate the custom drush commands for TFA.
 */

/**
 * Implements hook_drush_command().
 */
function import_json_drush_command() {
  $items = array();
  $items['start-json-import'] = [
    'description' => 'Start JSON import',
    'arguments' => [
    'name' => 'Start JSON Import',
  ],
  'drupal dependencies' => ['import_json'],
  'aliases' => ['tfa:json_import'],
  ];
  return $items;
}
/**
 * Call back function drush_custom_drush_command_say_hello()
 * The call back function name in the  following format
 *   drush_{module_name}_{item_id_for_command}()
 */
function drush_import_json_start_json_import($name = '') {
  $config = \Drupal::config('import_json.config');
  if ($config->get('bool__permit_drush_import_trigger')) {
    drush_log('TFA JSON import triggered', 'ok');
    if (extension_loaded('curl')) {
      // load service that triggers import
      $service = \Drupal::service('import_json.import');
      $service->triggerImportJSON(false);
    } else {
      drush_log('PHP CURL extension must be enabled', 'error');
    }
  } else {
    drush_log('Enable "Permit Drush to Trigger Import" in module configuration first');
  }
}
