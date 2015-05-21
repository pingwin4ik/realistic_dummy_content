<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\loggers;

use Drupal\realistic_dummy_content_api\loggers\Log;

class DrushLog implements Log {
  public function log($text, $vars = array()) {
    drush_log(dt($text, $vars), 'ok');
  }

  public function error($text, $vars = array()) {
    $this->log('_FAILURE');
    drush_set_error('_ERROR', dt($text, $vars));
    drush_set_context('DRUSH_ERROR_CODE', 1);
    // we need this for jenkins to get 1 to show up in $? With drush_die(1)
    // $? returns 0 in the command line.
    die(1);
  }
}
