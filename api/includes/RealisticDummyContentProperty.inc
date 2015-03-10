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
abstract class RealisticDummyContentProperty extends RealisticDummyContentAttribute {
  function GetType() {
    return 'property';
  }
}
