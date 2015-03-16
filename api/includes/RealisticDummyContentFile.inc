<?php

/**
 * @file
 *
 * Define RealisticDummyContentFile autoload class.
 */

/**
 * Represents files as groups.
 *
 * For example:
 *
 *     1.txt
 *     2.txt
 *     3.txt
 *
 * will be represented as three files, but
 *
 *     1.txt
 *     2.txt
 *     2.attribute.txt
 *     2.attribute1.txt
 *     3.txt
 *
 * will also be represented as three files, but the second one will have two
 * attributes, attribute and attribute1.
 *
 * This allows us to defined attributes or metadata for certain file types, for
 * example:
 *
 *   realistic_dummy_content/fields/node/article/
 *     - body/
 *       - ipsum.txt
 *       - ipsum.format.txt
 *       - lorem.txt
 *    - field_image/
 *       - 1.jpg
 *       - 2.jpg
 *       - 2.alt.txt
 *
 * In the above example, `realistic_dummy_content` sees two possible body values, _one of
 * which with a specific input format_; and two possible images, _one of which with a
 * specific alt text_. Attributes are never compulsory, and in the case where an attribute
 * is needed, a reasonable fallback value is used, for example `filtered_html` will be
 * used if no format is specified for the body.
 */
class RealisticDummyContentFile extends RealisticDummyContentAttribute {

  /**
   * Returns all files with a given extension for a given filepath.
   *
   * Files do not always have a one-to-one relationship with the filesystem.
   * For example:
   *
   *     1.txt
   *     2.txt
   *     3.txt
   *
   * will be represented as three files, but
   *
   *     1.txt
   *     2.txt
   *     2.attribute.txt
   *     2.attribute1.txt
   *     3.txt
   *
   * will also be represented as three files, but the second one will have two
   * attributes, attribute and attribute1.
   *
   * @param $filepath
   *   An absolute filepath on the system, for example /path/to/drupal/sites/all/
   *   modules/mymodule/realistic_dummy_content/fields/node/article/body
   * @param $extensions
   *   An array of extensions which should be taken into consideration.
   *
   * @return
   *
   * @throws
   *   Exception
   */
  static function GetAll($filepath, $extensions) {
    try {
      $candidate_files = file_scan_directory($filepath, '/.*\.(' . implode('|', $this->GetExtensions()) . ')$/');

      $files = $this->SortCandidateFiles($candidate_files);


      return array_values($files);
    }
    catch (Exception $e) {
      return array();
    }
  }

  /**
   * Given a list of candidate files, sort them by names and parts.
   *
   * @param $candidate_files
   *   An array keyed by uri which contains files, like this:
   *
   *     one.txt
   *     two.txt
   *     two.attribute.txt
   *     two.attribute1.txt
   *     three.txt
   *
   * @return
   *   A sorted array which looks like:
   *
   *     one => array('value' => one.txt),
   *     two = array(
   *        'value' => two.txt,
   *        'attribute' => two.attribute.txt,
   *        'attribute1' => two.attribute1.txt,
   *     ),
   *     three => array('value' => three.txt),
   *
   */
  static function SortCandidateFiles($candidate_files) {
    $files = array();
    // Add all except the README
    foreach ($candidate_files as $candidate_file) {
      $name = $this->Name($candidate_file);

      if (drupal_strtolower(trim($name)) != 'readme') {
        if (!isset($files[$name])) {
          $files[$name] = array();
        }
        $files[$name][$this->Attribute($candidate_file)] = $candidate_file;
      }
    }
    // We expect the files to be sorted alphabetically, which is not the case on all
    // systems.
    ksort($files);
  }

}
