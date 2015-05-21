<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\generators;

abstract class Generator {
  private $type;
  private $bundle;
  private $num;
  private $more;

  /**
   * @param $more = array()
   *   For future use.
   */
  function __construct($type, $bundle, $num, $more = array()) {
    $this->type = $type;
    $this->bundle = $bundle;
    $this->num = $num;
    if (isset($more['kill']) && $more['kill']) {
      $this->kill = TRUE;
    }
    else {
      $this->kill = FALSE;
    }
  }

  function GetBundle() {
    return $this->bundle;
  }

  function GetType() {
    return $this->type;
  }

  function GetKill() {
    return $this->kill;
  }

  function GetNum() {
    return $this->num;
  }

  function Generate() {
    $this->_Generate_();
  }
}
