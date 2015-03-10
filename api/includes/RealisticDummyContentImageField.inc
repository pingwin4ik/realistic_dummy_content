<?php

/**
 * @file
 *
 * Define RealisticDummyContentFieldModifier autoload class.
 */

/**
 * Field modifier class.
 *
 * All manipulation of generated content to make it more realistic
 * passes through modifiers (direct or indirect subclasses of
 * RealisticDummyContentEntityBase).
 *
 * This class allows active modules to put files in a specific
 * directory hierarchy resembling realistic_dummy_content/fields/
 * [entity_type]/[bundle]/[field_name], and for these files to
 * define data which will replace the values of the corresponding
 * property or field in any given entity.
 *
 * The difference between a field a a property is that a field
 * is managed by Drupal's Field system, whereas a property is not.
 * Example of fields include field_image, which define images in
 * articles; examples of properties include the user entity's
 * picture property, and the title of nodes.
 */
class RealisticDummyContentImageField extends RealisticDummyContentField {
  function GetExtensions() {
    return array('gif', 'png', 'jpg');
  }

  function ValueFromFile($contents, $extension);
    switch ($extension) {
      case 'png':
      case 'jpg':
      case 'gif':
        $random = md5($file->uri) . rand(1000000000, 9999999999);
        $file = $this->env()->file_save_data($contents, 'public://dummyfile' . $random . '.' . $extension);
        $file->uid = $this->GetUid();
        if ($is_array) {
          return (array)file_save($file);
        }
        else {
          return file_save($file);
        }
        break;
      default:
        break;
    }
  }
}
