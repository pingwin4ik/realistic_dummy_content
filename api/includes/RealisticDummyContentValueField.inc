<?php

/**
 * @file
 *
 * Define RealisticDummyContentFieldModifier autoload class.
 */

/**
 */
class RealisticDummyContentValueField extends RealisticDummyContentField {
  function ValueFromFile($contents, $extension) {
    if (in_array($extension, $this->GetTextExtensions())) {
      return array('value' => $contents);
    }
  }
}
