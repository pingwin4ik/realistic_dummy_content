<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\environments;

use Drupal\realistic_dummy_content_api\loggers\DrushLog;

class Drush {

  static function command() {
    $items['generate-realistic'] = array(
      'description' => dt('Generates realistic dummy content by looking in each active module for a file called realistic_dummy_content/recipe/module_name.recipe.inc, which should contain a subclass of Recipe called module_name_realistic_dummy_content_recipe with a run() method.'),
      'aliases' => array('grc'),
    );
    return $items;
  }

  static function generateRealistic() {
    realistic_dummy_content_api_apply_recipe(new DrushLog);
  }

}
