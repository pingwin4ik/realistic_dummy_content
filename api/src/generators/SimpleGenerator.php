<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\generators;

use Drupal\realistic_dummy_content_api\generators\Generator;

class SimpleGenerator extends Generator {
  /**
   * @throws
   *   \Exception
   */
  function _Generate_() {
    for ($i = 0; $i < $this->GetNum(); $i++) {
      $info = array(
        'devel_generate' => TRUE,
      );
      $entity = entity_create($this->GetType(), $info);
      $entity->save();
    }
  }

}
