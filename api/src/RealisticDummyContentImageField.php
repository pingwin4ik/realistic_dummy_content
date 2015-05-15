<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentImageField autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Field modifier for image fields.
 */
class RealisticDummyContentImageField extends \Drupal\realistic_dummy_content_api\RealisticDummyContentField {
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
        \Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED => array(
          (array)$file,
        ),
      );
    }
    return $return;
  }

}
