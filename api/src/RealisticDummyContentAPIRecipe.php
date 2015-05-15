<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentAPIRecipe autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

abstract class RealisticDummyContentAPIRecipe extends \Drupal\realistic_dummy_content_api\RealisticDummyContentRecipe {
  // Prior to beta4, \Drupal\realistic_dummy_content_api\RealisticDummyContentAPIRecipe was used. To avoid modifying
  // all sites which use this funcitonality, this class is included for
  // backward-compatibility with beta3 (see https://www.drupal.org/node/2451125).
}
