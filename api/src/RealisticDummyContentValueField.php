<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentFieldModifier autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Represents a generic field which appears in an entity object as
 * array('value' => 'xyz').
 */
class RealisticDummyContentValueField extends \Drupal\realistic_dummy_content_api\RealisticDummyContentField {
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
