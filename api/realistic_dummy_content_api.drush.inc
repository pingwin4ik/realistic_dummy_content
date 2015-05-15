<?php

/**
 * @file
 * Drush integration for the realistic_dummy_content_api module.
 */

// We must call interface_exists here because during the call
// to drush en \Drupal\realistic_dummy_content_api\RealisticDummyContent, the interface is not present yet.
if (interface_exists('\Drupal\realistic_dummy_content_api\RealisticDummyContentLog')) {
  class RealisticDummyContentDrushAPILog implements \Drupal\realistic_dummy_content_api\RealisticDummyContentLog {
    public function log($text, $vars = array()) {
      drush_log(dt($text, $vars), 'ok');
    }

    public function error($text, $vars = array()) {
      $this->log('\Drupal\realistic_dummy_content_api\RealisticDummyContent_FAILURE');
      drush_set_error('\Drupal\realistic_dummy_content_api\RealisticDummyContent_ERROR', dt($text, $vars));
      drush_set_context('DRUSH_ERROR_CODE', 1);
      // we need this for jenkins to get 1 to show up in $? With drush_die(1)
      // $? returns 0 in the command line.
      die(1);
    }
  }
}

/**
 * Implements hook_drush_command().
 */
function realistic_dummy_content_api_drush_command() {
  $items['generate-realistic'] = array(
    'description' => dt('Generates realistic dummy content by looking in each active module for a file called realistic_dummy_content/recipe/module_name.recipe.inc, which should contain a subclass of \Drupal\realistic_dummy_content_api\RealisticDummyContentRecipe called module_name_realistic_dummy_content_recipe with a run() method.'),
    'aliases' => array('grc'),
  );
  return $items;
}

/**
 * Callback: Generates realistic content
 */
function drush_realistic_dummy_content_api_generate_realistic() {
  realistic_dummy_content_api_apply_recipe(new \Drupal\realistic_dummy_content_api\RealisticDummyContentDrushAPILog);
}
