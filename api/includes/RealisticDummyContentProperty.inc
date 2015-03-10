<?php

/**
 * @file
 *
 * Define RealisticDummyContentProperty autoload class.
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
class RealisticDummyContentProperty extends RealisticDummyContentAttribute {
  /**
   * Change a property, if possible, based on a value found in a list of files.
   *
   * Entities have fields (such as field_image) and properties (such as
   * picture). They are dealt with differently. This function changes a
   * property.
   *
   * @param $files
   *   An array of file objects where we can fetch replacement content.
   * @param $property
   *   The name of the property to change.
   */
  function ChangeProperty($files, $property) {
    $value = $this->ValueFromFile($files);
    if ($value) {
      $entity = $this->GetEntity();
      if (is_array($value) && isset($value['value'])) {
        $value = $value['value'];
      }
      $entity->{$property} = $value;
      $this->SetEntity($entity);
    }
  }

}
