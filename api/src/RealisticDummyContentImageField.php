<?php

/**
 * @file
 *
 * Define RealisticDummyContentImageField autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Field modifier for image fields.
 */
class RealisticDummyContentImageField extends RealisticDummyContentField {
  /**
   * {@inheritdoc}
   */
  function GetExtensions() {
    return $this->GetImageExtensions();
  }

  /**
   * {@inheritdoc}
   */
  function ValueFromFile_($file) {
    if (!$file->Value()) {
      return NULL;
    }
    $return = NULL;
    $file = $this->ImageSave($file);
    if ($file) {
      $return = array(
        LANGUAGE_NONE => array(
          (array)$file,
        ),
      );
    }
    return $return;
  }

}
