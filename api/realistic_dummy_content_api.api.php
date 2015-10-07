<?php
/**
 * @file
 *
 * Hook definitions. These functions are never called, and are included
 * here for documentation purposes only.
 */

/**
 * @param &$class
 *   The original class name, fully qualified with its namespace, which is set to
 *   manipulate attributes of this type. For example, if the machine name of a field
 *   type is text_with_summary, because this is a field, Realistic Dummy Content will
 *   use the field manipulator by default, however, that is not enough to
 *   manipulate this field which is more complex (contains a summary, text format...)
 *   so another class will be used instead. If you are a developer, your module
 *   can define which class manipulates fields of given types. See the
 *   TextWithSummaryField class, included with this module, for an example.
 * @param &$machine_name
 *   The machine name of the field or property, for example "title",
 *   "text_with_summary", or "picture".
 */
function hook_realistic_dummy_content_attribute_manipulator_alter(&$class, &$type, &$machine_name) {
  // If you want to implement a particular manipulator class for a field or property
  // you can do so by implementing this hook and reproducing what's below for your
  // own field or property type.
  switch ($machine_name) {
    case 'picture': // the user picture
      $class = 'Drupal\realistic_dummy_content_api\attributes\UserPicture';
      break;
    case 'text_with_summary': // e.g. body
      $class = 'Drupal\realistic_dummy_content_api\attributes\TextWithSummaryField';
      break;
    case 'taxonomy_term_reference': // e.g. tags on articles
      $class = 'Drupal\realistic_dummy_content_api\attributes\TermReferenceField';
      break;
    case 'image': // e.g. images on articles
      $class = 'Drupal\realistic_dummy_content_api\attributes\ImageField';
      break;
    default:
      break;
  }
}

/**
 * hook_realistic_dummy_content_api_class().
 *
 * Return any object which is a subclass of Base, which
 * will be used to modify content which is deemed to be dummy content.
 *
 * @param $entity
 *   The object for a given type, for example this can be a user object
 *   or a node object.
 * @param $type
 *   The entity type of the information to change, for example 'user' or 'node'.
 *
 * @return
 *   Array of objects which are a subclass of Base.
 */
function hook_realistic_dummy_content_api_class($entity, $type) {
  return array(
    // Insert class names for all classes which can modify entities for the
    // given type. These classes must exist, either through Drupal's
    // autoload system or be included explictely, and they must be
    // subclasses of Base
    'FieldModifier',
  );
}

/**
 * hook_realistic_dummy_content_api_dummy().
 *
 * Return whether or not an object of a given type is a dummy object or not.
 * The motivation for this hook is for cases where you may not be using
 * devel_generate for nodes, or whether you have a specific technique for
 * determining whether or not a given object is dummy content or not.
 *
 * @param $entity
 *   The object for a given type, for example this can be a user object
 *   or a node object.
 * @param $type
 *   The type of the information to change, for example 'user' or 'node'.
 *
 * @return
 *   Boolean value representing whether or not this object is a dummy object.
 *   FALSE means we were unable to ascertain that the entity is in fact
 *   a dummy object. Other modules which implement this hook might
 *   determine that this is a dummy object.
 */
function hook_realistic_dummy_content_api_dummy($entity, $type) {
  $return = FALSE;
  switch ($type) {
    case 'node':
      if (isset($entity->devel_generate)) {
        return TRUE;
      }
      break;
    case 'user':
      // devel_generate puts .invalid at the end of the generated user's
      // email address. This module should not be activated on a production
      // site, or else anyone can put ".invalid" at the end of their email
      // address and their profile's content will be overridden.
      $suffix = '.invalid';
      if (drupal_substr($entity->mail, strlen($entity->mail) - strlen($suffix)) == $suffix) {
        return TRUE;
      }
      break;
    default:
      break;
  }
  return $return;
}
