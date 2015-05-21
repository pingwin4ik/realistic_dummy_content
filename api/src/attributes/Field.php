<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\attributes;
use Drupal\realistic_dummy_content_api\attributes\Attribute;

/**
 * Represents fields like body or field_image.
 */
abstract class Field extends Attribute {
  function GetType() {
    return 'field_config';
  }

}
