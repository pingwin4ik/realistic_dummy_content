<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\manipulators;

use Drupal\realistic_dummy_content_api\manipulators\EntityBase;
use Drupal\realistic_dummy_content_api\attributes\Field;
use Drupal\realistic_dummy_content_api\attributes\TextProperty;
use Drupal\realistic_dummy_content_api\attributes\ValueField;
use Drupal\realistic_dummy_content_api\environments\Drupal;
use Drupal\realistic_dummy_content_api\loggers\Exception;

/**
 * Field modifier class.
 *
 * All manipulation of generated content to make it more realistic
 * passes through modifiers (direct or indirect subclasses of
 * EntityBase).
 *
 * This class (\FieldModifier) allows active modules to put files
 * in a specific directory hierarchy resembling realistic_dummy_content/fields/
 * [entity_type]/[bundle]/[field_name], and for these files to define data which will
 * replace the values of the corresponding property or field in any given entity.
 *
 * The difference between a field and a property is that a field is managed by Drupal's
 * Field system, whereas a property is not. Example of fields include field_image, which
 * define images in articles (in a standard installation); examples of properties include
 * the user entity's picture property, and the title of nodes.
 *
 * Drupal stores field values differently depending on the type of field, and third-party
 * modules can define their own schemes for storing values; an extensible system has
 * been defined to allow any module (including this one) to define field formats
 * and interpret data from files. To do so, modules must implement
 * hook_realistic_dummy_content_field_manipular_alter(). Please see the example
 * in this module's .module file, with more documentation in
 * realistic_dummy_content_api.api.php. (Realistic Dummy Content API defines specific
 * manipulators for the fields image, text_with_summary, taxonomy_term_reference...).
 */
class FieldModifier extends EntityBase {
  /**
   * Get properties for the entity, for example user's picture or node's name.
   *
   * This is deprecated for Drupal 8
   *
   * @return
   *   An empty array is returned in case of an error.
   *   An array of Attribute objects, keyed by attribute name,
   *   e.g. title => [Attribute],
   *        field_image => [Attribute]
   */
  function GetProperties() {
    try {
      // Deprecated for Drupal 8.
      return array();
    }
    catch (\Exception $e) {
      return array();
    }
  }

  /**
   * Get fields for the entity, for example body or field_image.
   *
   * @return
   *   An empty array is returned in case of an error.
   *   An array of Attribute objects, keyed by attribute name,
   *   e.g. title => [\Drupal\realistic_dummy_content_api\attributes\Attribute],
   *        field_image => [...]
   */
  function GetFields() {
    try {
      $entity = $this->GetEntity();
      // We now have an entity object, for example \Drupal\node\Entity\Node
      $modifiable_fields = $entity->getFields();
      // $fields is now an array of things like nid, uuid, created, body,
      // field_image, etc.
      foreach ($modifiable_fields as $field => $object) {
        $this->AddModifier($modifiable_fields, 'field_config', $field);
      }
      return $modifiable_fields;
    }
    catch (\Exception $e) {
      return array();
    }
  }

  /**
   * Helper function, returns the original class and attribute type from type and name.
   *
   * @param $type
   *   Either 'property' or 'field_config'
   * @param $name
   *   Name of the property or field, for example 'nid', 'body', 'picture', 'title',
   *  'field_image'.
   *
   * @return
   *   Associative array with:
   *     original_class => string, corresponds to the fully qualified (with namespace)
   *       class name of the class which should manipulate this type of attribute. For
   *       example, any fields are manipulate by a "value field" manipulator, and and
   *       any properties (titles, created) are manipulated by a text property
   *       manipulator. Certain fields are complex, though, so this module provides
   *       an alter hook (see hook_realistic_dummy_content_attribute_manipulator_alter()).
   *     attribute_type => string, corresponds the type of attribute with which we are
   *       dealing, for example text_with_summary, title...
   *
   * @throws
   *   Exception
   */
  protected function getBaseInfo($type, $name) {
    $full_name = $this->getType() . '.' . $this->getBundle() . '.' . $name;
    $field_info = entity_load('field_config', $full_name);
    if (!$field_info) {
      // if an field cannot be loaded, then it is a property
      $type = 'property';
    }

    switch ($type) {
      case 'property':
        $original_class = '\Drupal\realistic_dummy_content_api\attributes\TextProperty';
        $attribute_type = $name;
        break;
      case 'field_config':
        $original_class = '\Drupal\realistic_dummy_content_api\attributes\ValueField';
        $field_info = entity_load('field_config', $full_name);
        if (!$field_info) {
          throw new Exception('Unable to load field_config entity named ' . $full_name);
        }
        $attribute_type = $field_info->getType();
        break;
      default:
        throw new Exception('Please use the type property or field_config');
        break;
    }
    $return = array(
      'original_class' => $original_class,
      'attribute_type' => $attribute_type,
    );
    return $return;
  }


  /**
   * Adds a modifier to a list of attribute modifiers.
   *
   * To abstract away the difference between fields and properties, we
   * call them all attributes. Modifiers will modify attributes depending on
   * what they are. For example, a user picture is modified differently than
   * an image in an article. This is managed through an extensible class
   * hierarchy. Modules, including this one, can implement
   * hook_realistic_dummy_content_attribute_manipular_alter() to determine
   * which class should modify which attribute (field or property).
   *
   * By default, we will consider that properties are text properties and that
   * fields' [value] property should be modified. This is not the case, however
   * for user pictures (which should load a file), body fields (which contain
   * a text format), and others. These are all defined in subclasses and can
   * be extended by module developers.
   *
   * @param &$modifiers
   *   Existing array of subclasses of Attribute, to which
   *   new modifiers will be added.
   * @param $type
   *   Either 'property' or 'field_config'
   * @param $name
   *   Name of the property or field, for example 'body', 'picture', 'title',
   *  'field_image'.
   *
   * @throws
   *   Exception
   */
  function AddModifier(&$modifiers, $type, $name) {
    $class = '';
    if (!$name) {
      throw new Exception('Name must not be empty');
    }
    if (!is_string($name)) {
      throw new Exception('Name must be a string');
    }
    $info = $this->getBaseInfo($type, $name);

    $original_class = $info['original_class'];
    $attribute_type = $info['attribute_type'];

    $class = $original_class;
    \Drupal::moduleHandler()->alter('realistic_dummy_content_attribute_manipulator', $class, $type, $attribute_type);

    if (!$class) {
      // third-parties might want to signal that certain fields cannot be
      // modified (they can be too complex for the default modifier and do not yet
      // have a custom modifier).
      return;
    }
    // @TODO check if class is abstract
    elseif (class_exists($class)) {
      $modifier = new $class($this, $name);
    }
    else {
      \Drupal::logger('realistic_dummy_content_api')->notice(t('Class does not exist: @c. This might be because a third-party module has implemented realistic_dummy_content_api_realistic_dummy_content_attribute_manipular_alter() with a class that cannot be implemented, or which is not fully qualified with its namespace. @original will used instead.', array('@c' => $class, '@original' => $original_class)));
      $modifier = new $original_class($this, $name);
    }

    if (isset($modifier)) {
      // It's OK to index by name because attributes and fields can never have
      // the same names.
      $modifiers[$name] = $modifier;
    }
  }

  /**
   * {@inheritdoc}
   */
  function Modify() {
    $attributes = $this->GetAttributes();
    foreach ($attributes as $attribute) {
      $attribute->Change();
    }
  }

  /**
   * Returns all fields and properties.
   *
   * We implement fields and properties as subclasses of the same parent class,
   * which defines a common interface for dealing with them.
   *
   * @return
   *   An empty array if an error occurred, or an array of Attribute
   *   objects, keyed by attribute name,
   *     title => [Attribute],
   *     field_image => [Attribute]
   */
  function GetAttributes() {
    try {
      return array_merge($this->GetFields(), $this->GetProperties());
    }
    catch (\Exception $e) {
      return array();
    }
  }

  /**
   * Generate a random number, or during tests, give the first available number.
   */
  function rand($start, $end) {
    $return = realistic_dummy_content_api_rand($start, $end, $this->GetHash());
    return $return;
  }

  /**
   * Get the uid property of this entity, or 0.
   *
   * @return
   *   The uid of the associated entity.
   */
  function GetUid() {
    $entity = $this->GetEntity();
    if (isset($entity->uid)) {
      return $entity->uid;
    }
    else {
      return 0;
    }
  }

}
