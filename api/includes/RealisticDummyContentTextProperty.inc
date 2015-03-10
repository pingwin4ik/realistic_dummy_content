<?php

/**
 * @file
 *
 * Define RealisticDummyContentUserPicture autoload class.
 */

/**
 */
class RealisticDummyContentTextProperty extends RealisticDummyContentProperty {
  function ValueFromFile($contents, $extension) {
    if (in_array($extension, $this->GetTextExtensions())) {
      return $contents;
    }
  }

}
