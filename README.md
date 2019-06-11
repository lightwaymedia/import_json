# import_json (Drupal 8 module)

A module that facilitates for the importing of posts and users from a public JSON resource. The sample JSON resources used to construct this demo functionality are:

- https://jsonplaceholder.typicode.com/posts
- https://jsonplaceholder.typicode.com/users

## Features:
- A module configuration/import execution page available to users with the role 'administrator' accessible in the admin menu via 'Configuration' > 'System' > 'Import JSON'
- Batch processing (when triggered via UI, not during the use of the drush command below as CLI timeouts are often more flexible and can additionally be more verbose)
- Drush command to trigger import: "drush import:json_import" (Drush 8.x)
- Import users functionality
- Checks for duplicates via the persistent id field from the source api resource; does not allow for duplicates for either users or posts
- As a bonus: users are linked to their posts as authors when the option to import both users/posts is enabled

## Requirements:
- Drupal 8
- PHP CURL extension enabled on server
- Drush 8.x (if you would like to use the Drush command)

## Installation:
- Place module in /modules
- drush en import_json (or via the Extend UI)
