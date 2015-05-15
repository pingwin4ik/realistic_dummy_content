<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentTextWithSummaryField autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Represents the text with summary field, which must have a text format when part
 * of an entity object. Node body is one example.
 */
class RealisticDummyContentTextWithSummaryField extends \Drupal\realistic_dummy_content_api\RealisticDummyContentField {
  /**
   * {@inheritdoc}
   */
  function ValueFromFile_($file) {
    $value = $file->Value();
    // @TODO use the site's default, not filtered_html, as the default format.
    $format = $file->Attribute('format', 'filtered_html');
    // If the value cannot be determined, which is different from an empty string.
    if ($value === NULL) {
      return NULL;
    }
    if ($value) {
      return array(
        \Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED => array(
          array(
            'value' => $value,
            'format' => $format,
          ),
        ),
      );
    }
    else {
      return array();
    }
  }

}
