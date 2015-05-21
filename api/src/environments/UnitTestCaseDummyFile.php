<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\environments;

/**
 * Dummy file, used to test how fields manage files
 */
class UnitTestCaseDummyFile {
  private $value;

  /**
   * Constructor.
   *
   * @param $value
   *   The value to return
   */
  function __construct($value) {
    $this->value = $value;
  }

  /**
   * Returns the dummy value.
   *
   * @return
   *   The value we used when creating this object.
   */
  function Value() {
    return $this->value;
  }
}

