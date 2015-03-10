<?php

/**
 * @file
 *
 * Define RealisticDummyContentAttribute autoload class.
 */

/**
 * Represents either a field or a property for an entity.
 *
 * Fields are for example field_image, or field_body, and attributes are
 * for example the title of a node and the image of a user.
 *
 * We want to abstract away the differences so we can treat both
 * the same way without using control statements in our code.
 */
abstract class RealisticDummyContentAttribute {
  /**
   * The entity is set on construction and is a subclass of
   * RealisticDummyContentEntityBase. It contains information about the
   * entity to which this field instance is attached.
   */
  private $entity;

  /**
   * The name of this attribuet, for example title, picture, field_image...
   */
  private $name;

  /**
   * @param $entity
   *   Subclass of RealisticDummyContentEntityBase.
   */
  function __construct($entity, $name) {
    $this->entity = $entity;
    $this->name = $name;
  }

  /**
   * Getter for $this->name
   */
  function GetName() {
    return $this->name;
  }

  /**
   * Getter for $this->entity
   */
  function GetEntity() {
    return $this->entity;
  }
  function GetBundle() {
    return $this->GetEntity()->GetBundle();
  }

  /**
   * Returns the type of this attribute.
   *
   * Drupal uses fields (managed by the field system) and properties to define
   * attributes of entities. Fields include body and field_image; properties include
   * title and the user picture.
   *
   * @return
   *   'property' or 'field'
   */
  abstract function GetType();

  /**
   * Changes this attribute by looking for data in files.
   *
   * Any module can define a file hierarchy to determine realistic dummy data
   * for this attribute. See the ./realistic_dummy_content/ folder for an example.
   *
   * This function checks the filesystem for compatible files (for example, only
   * image files are acceptable candidate files for field_image), choose one
   * through the selection mechanism (random or sequential), and then procedes to
   * change the data for the associated field for this class.
   */
  function Change() {
    $files = $this->GetCandidateFiles();
    $this->ChangeFromFiles($files);
  }

  /**
   * Given candidate files, change the value of this attribute based on one of them.
   *
   * @param $files
   *   An array of files.
   */
  function ChangeFromFiles($files) {
    $value = $this->ValueFromFiles($files);
    if ($value) {
      $this->Change($file);
    }
  }

  /**
   * Get acceptable file extensions which contain data for this attribute.
   *
   * For example, title attributes can be replaced by data in txt files, whereas as
   * picture and field_image attributes require png, jpg, gif.
   *
   * @return
   *   An array of acceptable file extensions.
   */
  function GetExtensions() {
    return array();
  }

  /**
   * Get all candidate files for a given field for this entity.
   */
  function GetCandidateFiles() {
    $candidate_files = array();
    foreach (module_list() as $module) {
      $filepath = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . '/realistic_dummy_content/fields/' . $this->GetType() . '/' . $this->GetBundle() . '/' . $this->GetName();
      $candidate_files = array_merge($candidate_files, array_values(file_scan_directory($filepath, '/.*\.(' . implode('|', $this->GetExtensions()). ')$/')));
    }
    $files = array();
    foreach ($candidate_files as $candidate_file) {
      if ($candidate_file->filename != 'README.txt') {
        $files[$candidate_file->filename] = $candidate_file;
      }
    }
    // We expect the files to be sorted alphabetically, which is not the case on all systems.
    ksort($files);
    return array_values($files);
  }

  /**
   *
   * @return
   *   In case of an error, return NULL.
   */
  abstract function ValueFromFile($contents, $extension);

  /**
   * Given a list of files, return a value from one of them randomly.
   *
   * @TODO this function only supports image fields, picture properties, and
   * text areas for now, see https://drupal.org/node/2266639.
   *
   * @param $files
   *   An array of file objects
   *
   * @param $is_array
   *   In the case where the file object itself is being returned, should
   *   it be returned as an array as opposed to an object. (The user's "picture"
   *   property expects a different format than file fields).
   *
   * @return
   *   A file object or array, or an associative array with the keys "value" and
   *   "format", or NULL if there are no files to choose from or the files have
   *   the wrong extension.
   *
   * @throws
   *   Exception.
   */
  function ValueFromFiles($files) {
    if (count($files)) {
      $rand_index = $this->rand(0, count($files) - 1);
      $file = $files[$rand_index];
      $filename = $file->filename;
      $extension = pathinfo($filename, PATHINFO_EXTENSION);
      $contents = $this->env()->file_get_contents($file->uri);
      return $this->ValueFromFile(trim($contents), $extension);
    }
  }

  function GetImageExtensions() {
    return array('gif', 'png', 'jpg');
  }

  function GetTextExtensions() {
    return array('txt');
  }

  function ImageSave($contents, $extension) {
    if (in_array($extension, $this->GetImageExtensions())) {
      return $this->FileSave();
    }
  }

  function FileSave($contents, $extension) {
    $random = md5($file->uri) . rand(1000000000, 9999999999);
    $file = $this->env()->file_save_data($contents, 'public://dummyfile' . $random . '.' . $extension);
    $file->uid = $this->GetUid();
    return file_save($file);
  }
}
