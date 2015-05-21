<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\attributes;

use Drupal\realistic_dummy_content_api\attributes\Field;

/**
 * Represents a generic field which appears in an entity object as
 * array('value' => 'xyz').
 */
class ValueField extends Field {
  /**
   * {@inheritdoc}
   */
  function ValueFromFile_($file) {
    $value = $file->Value();
    if ($value === NULL) {
      return;
    }
    return array(
      \Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED => array(
        array(
          'value' => $value,
        ),
      ),
    );;
  }

}
