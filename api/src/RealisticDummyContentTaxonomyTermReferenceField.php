<?php

/**
 * @file
 *
 * Define \Drupal\realistic_dummy_content_api\RealisticDummyContentTermReferenceField autoload class.
 */

namespace Drupal\realistic_dummy_content_api;

/**
 */
class RealisticDummyContentTermReferenceField extends \Drupal\realistic_dummy_content_api\RealisticDummyContentField {
  /**
   * {@inheritdoc}
   */
  function ValueFromFile_($file) {
    try {
      $termname = $file->Value();
      if ($termname) {
        return array(
          \Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED => array(
            array(
              'tid' => $this->GetTid($termname),
            ),
          ),
        );
      }
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Returns the term id for a term which is either existing or created on the fly.
   *
   * Let's say an entity (node) contains a term reference to the taxonomy vocabulary
   * "location", and in the realistic dummy content file structure, "Australia" is
   * used for the location. If "Australia" exists as a "location", then this function
   * will return its tid. If not, the term will be created, and then the tid will be
   * returned.
   *
   * @param $name
   *   The string for the taxonomy term.
   *
   * @return
   *   The associated pre-existing or just-created tid.
   *
   * @throws
   *   \Exception
   */
  function GetTid($name) {
    $vocabularies = taxonomy_get_vocabularies();
    $field_info = field_info_field($this->GetName());
    $candidate_existing_terms = array();
    foreach ($field_info['settings']['allowed_values'] as $vocabulary) {
      $vocabulary_name = $vocabulary['vocabulary'];
      foreach ($vocabularies as $vocabulary) {
        if ($vocabulary->machine_name == $vocabulary_name) {
          $tree = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree($vocabulary->vid);
          $candidate_existing_terms = array_merge($candidate_existing_terms, $tree);
        }
      }
    }
    foreach ($candidate_existing_terms as $candidate_existing_term) {
      if ($candidate_existing_term->name == $name) {
        return $candidate_existing_term->tid;
      }
    }

    if (!isset($vocabulary->vid)) {
      throw new \Exception('Expecting the taxonomy term reference to reference at least one vocabulary');
    }

    $term_values['name'] = $name;
    $term_values['vid'] = $vocabulary->vid;
    $term = entity_save('term', $term_values);
    if ($term->tid) {
      return $term->tid;
    }
    else {
      throw new \Exception('tid could not be determined');
    }
  }

}
