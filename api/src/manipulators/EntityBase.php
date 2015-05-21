<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\manipulators;

use Drupal\realistic_dummy_content_api\manipulators\Base;

/**
 * Generic entity manipulator.
 *
 * Class with an abstract Modify() method. Subclasses can have
 * access to entities in order to override demo content in them.
 */
abstract class EntityBase extends Base {
  private $hash;
  private $entity;
  private $type;

  /**
   * Constructor.
   *
   * @param $entity
   *   The entity object.
   * @param $type
   *   The entity type of the object, for example user or node.
   */
  function __construct($entity, $type) {
    $this->entity = $entity;
    $this->hash = md5(serialize($entity));
    $this->type = $type;
  }

  /**
   * Getter for the entity.
   */
  function GetEntity() {
    return $this->entity;
  }

  /**
   * Getter for the hash which uniquely identifies this entity.
   */
  function GetHash() {
    return $this->hash;
  }

  /**
   * Updates the entity object.
   *
   * Used by functions which manipulate fields and properties. Once they
   * are done with the manipulations, they update the entity using this
   * function.
   */
  function SetEntity($entity) {
    $this->entity = $entity;
  }

  /**
   * Get the entity type of the entity being manipulated.
   *
   * All entities must have a type and a bundle. The type can be node,
   * user, etc. and the bundle can be article, page. In case of a user,
   * there must be a bundle even if there is only one: it is called user,
   * like the entity type.
   *
   * @return
   *   The entity type, for example "node" or "user".
   */
  function GetType() {
    $return = $this->type;
    return $return;
  }

  /**
   * Get the bundle of the entity being manipulated.
   *
   * All entities must have a type and a bundle. The type can be node,
   * user, etc. and the bundle can be article, page. In case of a user,
   * there must be a bundle even if there is only one: it is called user,
   * like the entity type.
   *
   * @return
   *   The bundle, for example "article" or "user". Is a bundle is not
   *   readily available, return the entity type.
   */
  function GetBundle() {
    $entity = $this->GetEntity();
    if (isset($entity->type)) {
      return $entity->type;
    }
    else {
      return $this->GetType();
    }
  }

  /**
   * Modify the entity.
   *
   * Subclasses of EntityBase need to override
   * this function to perform modifications on the entity.
   */
  abstract function Modify();

}
