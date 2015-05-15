<?php

namespace Drupal\realistic_dummy_content_api;

/**
 * This log class can be used whenever you need a \Drupal\realistic_dummy_content_api\RealisticDummyContentLog
 */
class RealisticDummyContentDebugLog implements \Drupal\realistic_dummy_content_api\RealisticDummyContentLog {
  public function log($text, $vars = array()) {
    debug(t($text, $vars));
  }

  public function error($text, $vars = array()) {
    debug(t($text, $vars));
  }
}
