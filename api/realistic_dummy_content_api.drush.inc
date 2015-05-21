<?php

/**
 * @file
 * Drush integration for the realistic_dummy_content_api module.
 */

use Drupal\realistic_dummy_content_api\environments\Drush;

/**
 * Implements hook_drush_command().
 */
function realistic_dummy_content_api_drush_command() {
  Drush::command();
}

/**
 * Callback: Generates realistic content
 */
function drush_realistic_dummy_content_api_generate_realistic() {
  Drush::generateRealistic();
}
