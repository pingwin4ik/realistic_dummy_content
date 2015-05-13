<?php

/**
 * @file
 *
 * Define RealisticDummyContentField autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Represents fields like body or field_image.
 */
abstract class RealisticDummyContentField extends RealisticDummyContentAttribute {
  function GetType() {
    return 'field';
  }

}
