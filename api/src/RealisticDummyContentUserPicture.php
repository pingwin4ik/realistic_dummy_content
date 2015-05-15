<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentUserPicture autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 * Represents the user picture
 */
class RealisticDummyContentUserPicture extends \Drupal\realistic_dummy_content_api\RealisticDummyContentProperty {
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
    $file = $this->ImageSave($file);
    if ($file) {
      return $file;
    }
  }

}
