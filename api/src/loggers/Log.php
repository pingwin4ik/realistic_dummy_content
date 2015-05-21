<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\loggers;

/**
 * Interface for a log class
 */
interface Log {
  public function log($text, $vars = array());
  public function error($text, $vars = array());
}

