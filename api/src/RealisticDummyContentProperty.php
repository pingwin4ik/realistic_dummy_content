<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentProperty autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Represents properties like the user picture or node titles.
 */
abstract class RealisticDummyContentProperty extends \Drupal\realistic_dummy_content_api\RealisticDummyContentAttribute {
  /**
   * {@inheritdoc}
   */
  function GetType() {
    return 'property';
  }

}
