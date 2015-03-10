<?php

/**
 * @file
 *
 * Define RealisticDummyContentAttribute autoload class.
 */

/**
 *
 */
class RealisticDummyContentAttribute {
  private $name;
  function __construct($name) {
    $this->name = $name;
  }
  function GetName() {
    return $this->name;
  }

  function Change() {
    $files = $this->GetCandidateFiles();
    $attribute->ChangeFromFiles($files);
  }

  function ChangeFromFiles($files) {
    $value = $this->ValueFromFiles($files);
    if ($value) {
      $this->ChangeFromFile($file);
    }

  }

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
  function ValueFromFiles($files, $is_array = FALSE) {
    if (count($files)) {
      $rand_index = $this->rand(0, count($files) - 1);
      $file = $files[$rand_index];
      $filename = $file->filename;
      $extension = pathinfo($filename, PATHINFO_EXTENSION);
      $contents = $this->env()->file_get_contents($file->uri);
      return $this->ValueFromFile($contents, $extension);
    }
  }

}
