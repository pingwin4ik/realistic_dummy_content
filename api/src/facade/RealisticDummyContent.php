<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\realistic_dummy_content_api\facade;

use Drupal\realistic_dummy_content_api\manipulators\Base;
use Drupal\realistic_dummy_content_api\attributes\Field;
use Drupal\realistic_dummy_content_api\manipulators\FieldModifier;
use Drupal\realistic_dummy_content_api\attributes\ImageField;
use Drupal\realistic_dummy_content_api\loggers\Log;
use Drupal\realistic_dummy_content_api\recipes\Recipe;
use Drupal\realistic_dummy_content_api\attributes\TermReferenceField;
use Drupal\realistic_dummy_content_api\attributes\TextWithSummaryField;
use Drupal\realistic_dummy_content_api\attributes\UserPicture;

class RealisticDummyContent {
  /**
   * Type of number generation.
   */
  const REALISTIC_DUMMY_CONTENT_SEQUENTIAL = FALSE;
  const REALISTIC_DUMMY_CONTENT_RANDOM = TRUE;

  /**
   * Implements hook_entity_insert().
   */
  static function entity_presave(\Drupal\Core\Entity\EntityInterface $entity) {
    try {
      $type = $entity->getEntityType();
      if (RealisticDummyContent::is_dummy($entity, $type)) {
        $candidate = $entity;
        RealisticDummyContent::improve_dummy_content($candidate, $type);
        RealisticDummyContent::validate($candidate, $type);
        //$entity = $candidate;
      }
    }
    catch (\Exception $e) {
      drupal_set_message(t('realistic_dummy_content_api failed to modify dummy objects: message: @m', array('@m' => $e->getMessage())), 'error', FALSE);
    }
  }

  /**
   * Implements hook_realistic_dummy_content_attribute_manipulator_alter().
   */
  static function realistic_dummy_content_attribute_manipulator_alter(&$class, &$type, &$machine_name) {
    // If you want to implement a particular manipulator class for a field or property
    // you can do so by implementing this hook and reproducing what's below for your
    // own field or property type.
    switch ($machine_name) {
      case 'picture': // the user picture
        $class = 'UserPicture';
        break;
      case 'text_with_summary': // e.g. body
        $class = 'TextWithSummaryField';
        break;
      case 'taxonomy_term_reference': // e.g. tags on articles
        $class = 'TermReferenceField';
        break;
      case 'image': // e.g. images on articles
        $class = 'ImageField';
        break;
      default:
        break;
    }
  }

  /**
   * Checks if a given entity is dummy content.
   *
   * @param $entity
   *   The object for a given entity type, for example this can be a user object
   *   or a node object.
   * @param $type
   *   The type of the information to change, for example 'user' or 'node'.
   *
   * @return
   *   TRUE if at least one module implemented hook_realistic_dummy_content_api_dummy
   *   and thinks the entity is a dummy objects; FALSE otherwise.
   */
  static function is_dummy($entity, $type) {
    foreach (\Drupal::moduleHandler()->invokeAll('realistic_dummy_content_api_dummy', array($entity, $type)) as $dummy) {
      if ($dummy) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Insert or improve dummy data in an entity of a given type.
   *
   * @param $entity
   *   The object for a given type, for example this can be a user object
   *   or a node object.
   * @param $type
   *   The type of the information to change, for example 'user' or 'node'.
   *
   * @throws
   *   \Exception
   */
  static function improve_dummy_content(&$entity, $type) {
    $modifiers = \Drupal::moduleHandler()->invokeAll('realistic_dummy_content_api_class', array($entity, $type));
    foreach ($modifiers as $modifier_class) {
      RealisticDummyContent::validate_class($modifier_class);
      $modifier = new $modifier_class($entity, $type);
      $modifier->Modify();
      $entity = $modifier->GetEntity();
    }
  }

  /**
   * Throw an \Exception if an entity is not valid
   */
  static function validate($entity, $type) {
    // Throw an \Exception here if an entity is not valid, for example if two users
    // have the same email address or name, or anything else.
    // @TODO provide a hook for third-party modules to manage this.
  }

  /**
   * Validate that a class is a valid subclasss of Base
   *
   * @param $class
   *   A class name
   *
   * @throws
   *   \Exception
   */
  static function validate_class($class) {
    if (!class_exists($class)) {
      throw new \Exception(t('@class is not a valid class; make sure you include its file or use Drupal\'s autoload mechanism: name your include file with the same name as the class, and add it to the .info file, then clear your cache.', array('@class' => $class)));
    }
    if (!is_subclass_of($class, 'Base')) {
      throw new \Exception(t('@class is a valid class but it is not a subclass of Base.', array('@class' => $class)));
    }
  }

  /**
   * Implements hook_realistic_dummy_content_api_class().
   */
  static function realistic_dummy_content_api_class($entity, $type) {
    return array(
      // Insert class names for all classes which can modify entities for the
      // given type. These classes must exist, either through Drupal's
      // autoload system or be included explictely, and they must be
      // subclasses of Base
      'FieldModifier',
    );
  }

  /**
   * Implements hook_realistic_dummy_content_api_dummy().
   */
  static function realistic_dummy_content_api_dummy($entity, $type) {
    $return = FALSE;
    // Any entity with the devel_generate property set should be considered
    // dummy content. although not all dummy content has this flag set.
    // See https://drupal.org/node/2252965
    // See https://drupal.org/node/2257271
    if (isset($entity->devel_generate)) {
      return TRUE;
    }
    switch ($type) {
      case 'user':
        // devel_generate puts .invalid at the end of the generated user's
        // email address. This module should not be activated on a production
        // site, or else anyone can put ".invalid" at the end of their email
        // address and their profile's content will be overridden.
        $suffix = '.invalid';
        if (isset($entity->mail) && \Drupal\Component\Utility\Unicode::substr($entity->mail, strlen($entity->mail) - strlen($suffix)) == $suffix) {
          return TRUE;
        }
        break;
      default:
        break;
    }
    return $return;
  }

  /**
   * Generate a random, or sequential, number
   *
   * By default, this function will return a random number between $start and $end
   * inclusively. If you set the realistic_dummy_content_api_rand variable to
   * REALISTIC_DUMMY_CONTENT_SEQUENTIAL, for example for automated tested or in a recipe
   * (an example can be found at realistic_dummy_content/recipe/realistic_dummy_content.recipe.inc),
   * then this will call realistic_dummy_content_api_sequential().
   *
   * See the documentation for realistic_dummy_content_api_sequential() for details.
   *
   * @param $start
   *   The first possible number in the range.
   * @param $end
   *   The last possible number in the range.
   * @param $hash = NULL
   *   Ignored for random numbers; for sequential numbers, please se the documentation for
   *   realistic_dummy_content_api_sequential() for details.
   *
   * @return
   *   A random number by default, or a sequential number if you set the
   *   realistic_dummy_content_api_rand variable to REALISTIC_DUMMY_CONTENT_SEQUENTIAL.
   *   Please see the description of realistic_dummy_content_api_sequential() for details.
   */
  static function rand($start, $end, $hash = NULL) {
    if (\Drupal::state()->get('realistic_dummy_content_api_rand') ?: RealisticDummyContent::REALISTIC_DUMMY_CONTENT_RANDOM) {
      return rand($start, $end);
    }
    else return RealisticDummyContent::sequential($start, $end, $hash);
  }

  /**
   * Generate sequential number based on a hash
   *
   * Returns the starting number on every call until the hash is changed, at which case it
   * returns the second number, and so on.
   *
   * The idea behind this is that for a single node, we might want to retrieve the
   * 3rd file for each field (they go together).
   *
   * In the above example, if the 3rd file does not exist, we will return the first file,
   * in order to never return a number which is outside the range of start to end.
   *
   * @param $start
   *   The first possible number in the range.
   * @param $end
   *   The last possible number in the range.
   * @param $hash
   *   The number returned by this function will be in sequence: each call to
   *   realistic_dummy_content_api_sequential()'s return is incremented by
   *   one, unless $hash is the same as in the last call, in which case the return will the
   *   same as in the last call.
   *
   * @return
   *   A sequential number based on the $hash.
   *   Please see the description of the $hash parameter, above.
   */
  static function sequential($start, $end, $hash) {
    static $static_hash = NULL;
    if (!$static_hash) {
      $static_hash = $hash;
    }
    static $current = NULL;
    if (!$current) {
      $current = $start;
    }
    if ($static_hash != $hash) {
      $static_hash = $hash;
      $current -= $start;
      $current++;
      $current %= ($end - $start + 1);
      $current += $start;
    }

    if ($current > $end) {
      $return = $end;
    }
    elseif ($current < $start) {
      $return = $start;
    }
    else {
      $return = $current;
    }

    return $return;
  }


  /**
   * Attempts to generate all realistic content for the current site.
   *
   * @param $log
   *   A class which implements the interface Log. Logging
   *   will be different if you are using drush or in the context of an automated
   *   test, for example.
   */
  static function apply_recipe($log) {
    try {
      Recipe::Run($log);
    }
    catch (\Exception $e) {
      $log->log('An \Exception occurred while trying to apply a recipe');
      $log->error($e->getMessage());
    }
  }
}
