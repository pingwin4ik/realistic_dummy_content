<?php
/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentSimpleGenerator autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

class RealisticDummyContentSimpleGenerator extends \Drupal\realistic_dummy_content_api\RealisticDummyContentGenerator {
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
