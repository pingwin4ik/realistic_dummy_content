<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\attributes;

/**
 * Represents a text property like a node title or user name.
 */
class TextProperty extends Property {
  /**
   * {@inheritdoc}
   */
  function ValueFromFile_($file) {
    return $file->Value();
  }

}
