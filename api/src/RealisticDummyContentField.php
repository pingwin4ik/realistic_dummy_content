<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentField autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Represents fields like body or field_image.
 */
abstract class RealisticDummyContentField extends \Drupal\realistic_dummy_content_api\RealisticDummyContentAttribute {
  function GetType() {
    return 'field_config';
  }

}
