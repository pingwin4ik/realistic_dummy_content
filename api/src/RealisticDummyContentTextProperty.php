<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentTextProperty autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Represents a text property like a node title or user name.
 */
class RealisticDummyContentTextProperty extends \Drupal\realistic_dummy_content_api\RealisticDummyContentProperty {
  /**
   * {@inheritdoc}
   */
  function ValueFromFile_($file) {
    return $file->Value();
  }

}
