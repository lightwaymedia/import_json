<?php

namespace Drupal\import_json;

/**
 * Class CreateEntities.
 *
 */
class CreateEntities {

  public $config;

  public static function CreateEntities($entity_type, $entities, $is_drush_bool = false) {
    $config = \Drupal::config('import_json.config');
    $debug = ($config->get('bool__display_names_of_created_entities_as_message')) ? true : false;
    $bool__link_users = ($config->get('bool__link_users_to_posts')) ? true : false;
    foreach ($entities as $entity) {
      switch ($entity_type) {
      case 'posts':
        $id = (!empty($entity['id']) ? $entity['id'] : '');
        $user_id = (!empty($entity['userId']) ? $entity['userId'] : '');
        $title = (!empty($entity['title']) ? $entity['title'] : '');
        $body = (!empty($entity['body']) ? $entity['body'] : '');
        // find existing node with same import id
        $query = \Drupal::entityQuery('node');
        $count = $query->condition('type', 'page')
        ->condition('import_id', $id)
        ->count()
        ->execute();
        if ($count == 0) {
          $node = \Drupal\node\Entity\Node::create([
            'type' => 'page',
            'title' => t($title),
            'body' => array('value' => $body, 'format' => 'basic_html'),
            'import_id' => $id,
            ]);
          if ($bool__link_users) {
            // find user that is author
            $authors_query = \Drupal::entityQuery('user');
            $authors_query->condition('field_import_id', $user_id);
            $authors = $authors_query->execute();
            $author_user_id = (!empty($authors)) ? key($authors) : NULL;
            if (!empty($author_user_id)) $node->uid = $author_user_id; // set author
          }
          $node->save();
          if ($debug) drupal_set_message('New post: ' . $title); // module config option: "Display the Name(s) of Created Entities After Execution"
          if ($is_drush_bool && function_exists('drush_log')) drush_log('New post: ' . $title); // prints title on drush console if import:json_import used
        }
        break;

      case 'users':
        $id = (!empty($entity['id']) ? $entity['id'] : '');
        $name = (!empty($entity['name']) ? $entity['name'] : '');
        $email = (!empty($entity['id']) ? $entity['email'] : '');
        $username = (!empty($entity['id']) ? $entity['username'] : '');
        // find existing node with same import id
        $query = \Drupal::entityQuery('user');
        $count = $query->condition('field_import_id', $id)
        ->count()
        ->execute();
        if ($count == 0) {
          $user = \Drupal\user\Entity\User::create();
          $user->set('init', $email);
          $user->enforceIsNew();
          $user->setUsername($username);
          $user->setEmail($email);
          $user->set('field_import_id', $id);
          $user->save();
          if ($debug) drupal_set_message('New user: ' . $name); // module config option: "Display the Name(s) of Created Entities After Execution"
          if ($is_drush_bool && function_exists('drush_log')) drush_log('New user: ' . $name); // prints title on drush console if import:json_import used
        }
        break;
      }
    }
  }

  public static function CreateEntitiesFinishedCallback() {
    drupal_set_message('JSON import script completed.');
  }
}
