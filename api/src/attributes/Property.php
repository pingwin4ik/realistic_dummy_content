<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\attributes;
use Drupal\realistic_dummy_content_api\attributes\Attribute;

// use Drupal\realistic_dummy_content_api\attributes\Property;

/**
 * Represents properties like the user picture or node titles.
 */
abstract class Property extends Attribute {
  /**
   * {@inheritdoc}
   */
  function GetType() {
    return 'property';
  }

}
