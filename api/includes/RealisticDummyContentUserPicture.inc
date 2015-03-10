<?php

/**
 * @file
 *
 * Define RealisticDummyContentUserPicture autoload class.
 */

/**
 */
class RealisticDummyContentUserPicture extends RealisticDummyContentProperty {
  function ValueFromFile($contents, $extension) {
    $file = $this->ImageSave($contents, $extension);
    if ($file) {
      return (array)$file;
    }
  }

}
