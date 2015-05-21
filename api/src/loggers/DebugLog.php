<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\loggers;

use Drupal\realistic_dummy_content_api\loggers\Log;

/**
 * This log class can be used whenever you need a Log
 */
class DebugLog implements Log {
  public function log($text, $vars = array()) {
    debug(t($text, $vars));
  }

  public function error($text, $vars = array()) {
    debug(t($text, $vars));
  }
}
