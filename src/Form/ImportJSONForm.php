<?php

namespace Drupal\import_json\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ImportJSONForm
 *
 */
class ImportJSONForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return [
    'import_json.config',
    ];
  }
  
  public function getFormId() {
    return 'import_json_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    if (extension_loaded('curl')) {
      $config = $this->config('import_json.config');

      $form['bool_posts_import_enabled'] = array(
        '#type' => 'checkbox',
        '#title' => t('Posts import enabled'),
        '#default_value' => $config->get('bool_posts_import_enabled'),
      );

      $form['posts_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Posts JSON Resource URL'),
        '#default_value' => $config->get('posts_url'),
        '#description' => t('Try it with https://jsonplaceholder.typicode.com/posts'),
      );

      $form['bool_users_import_enabled'] = array(
        '#type' => 'checkbox',
        '#title' => t('Users Import Enabled'),
        '#default_value' => $config->get('bool_users_import_enabled'),
      );

      $form['users_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Users JSON Resource URL'),
        '#default_value' => $config->get('users_url'),
        '#description' => t('Try it with https://jsonplaceholder.typicode.com/users'),
      );

      $form['bool__link_users_to_posts'] = array(
        '#type' => 'checkbox',
        '#title' => t('Link Users to Imported Posts'),
        '#default_value' => $config->get('bool__link_users_to_posts'),
        '#description' => t('Set the author of imported posts to Drupal users whose "Import ID" field matches that of the "userID" field in the JSON import'),
      );

      $form['bool__display_names_of_created_entities_as_message'] = array(
        '#type' => 'checkbox',
        '#title' => t('Display the Name(s) of Created Entities After Execution'),
        '#default_value' => $config->get('bool__display_names_of_created_entities_as_message'),
        '#description' => t('The name(s) of created entities (users and/or posts) will be displayed as a Drupal message after the successful execution of the import'),
      );

      $form['bool__permit_drush_import_trigger'] = array(
        '#type' => 'checkbox',
        '#title' => t('Permit Drush to Trigger Import'),
        '#default_value' => $config->get('bool__permit_drush_import_trigger'),
        '#description' => t('Use <code>drush import:json_import</code> to trigger import when enabled'),
      );

      $form['bool__run_import_on_save'] = array(
        '#type' => 'checkbox',
        '#title' => t('Run Import On Save'),
        '#default_value' => $config->get('bool__run_import_on_save'),
        '#description' => t('Will trigger the import of entities (users and/or posts, depending on options configured above) on the selection of <em>"Save Changes / Import (if enabled)"</em> below'),
      );
      
      $form['run_import'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Save Changes / Import (if enabled)'),
      );
    } else {
      $form['markup'] = array(
        '#type' => 'markup',
        '#markup' => t('PHP CURL extension does not appear to be enabled. Please check PHP configuration on this system (hint: echo phpinfo())'),
      );
    }

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('import_json.config')
    ->set('bool_posts_import_enabled', $form_state->getValue('bool_posts_import_enabled'))
    ->set('posts_url', $form_state->getValue('posts_url'))
    ->set('bool_users_import_enabled', $form_state->getValue('bool_users_import_enabled'))
    ->set('users_url', $form_state->getValue('users_url'))
    ->set('bool__link_users_to_posts', $form_state->getValue('bool__link_users_to_posts'))
    ->set('bool__display_names_of_created_entities_as_message', $form_state->getValue('bool__display_names_of_created_entities_as_message'))
    ->set('bool__permit_drush_import_trigger', $form_state->getValue('bool__permit_drush_import_trigger'))
    ->set('bool__run_import_on_save', $form_state->getValue('bool__run_import_on_save'))
    ->save();

    if ($form_state->getValue('bool__run_import_on_save')) {
      // load service that triggers import
      $service = \Drupal::service('import_json.import');
      $service->triggerImportJSON();
    } else {
      drupal_set_message(t('Changes saved (import not executed per configuration setting)'));
    }
  }
}
