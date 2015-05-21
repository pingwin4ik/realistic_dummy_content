<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\attributes;

/**
 * Represents the user picture
 */
class UserPicture extends Property {
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
