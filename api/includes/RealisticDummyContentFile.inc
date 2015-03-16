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
class RealisticDummyContentFile {

  // The file array will look something like this:
  // array(
  //   'body.txt' => array(
  //     'format' => 'body.format.txt',
  //   ),
  // );
  private $file_array;

  /**
   * Constructor for a file object
   *
   * Several actual files can reside in the same file object if their names have
   * the same radical, for example:
   *
   *   a.b.c
   *   a.c
   *
   * have the same radical, a.c.
   *
   * @param $file_array
   * An array in the format:
   *  array(
   *   'body.txt' => array(
   *     'format' => 'body.format.txt',
   *   ),
   * );
   */
  function __construct($file_array) {
    $this->file_array = $file_array;
  }

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
      $candidate_files = file_scan_directory($filepath, '/.*\.(' . implode('|', $extensions) . ')$/');

      $files = self::SortCandidateFiles($candidate_files);

      $return = array();
      foreach ($files as $file) {
        $return[] = new RealisticDummyContentFile($file);
      }
      return $return;
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
      $name = self::Radical($candidate_file);

      if (drupal_strtolower(trim($name)) != 'readme') {
        if (!isset($files[$name])) {
          $files[$name] = array();
        }
        $attribute = self::Attribute($candidate_file);
        if ($attribute) {
          $files[$name][self::Attribute($candidate_file)] = $candidate_file;
        }
      }
    }
    // We expect the files to be sorted alphabetically, which is not the case on all
    // systems.
    ksort($files);
    return $files;
  }

  /**
   * Returns the name radical of a filename.
   *
   * The following examples will all return "two.txt"
   *
   *     two.txt
   *     two.attribute.txt
   *     two.attribute1.txt
   *
   * If >2 periods are present in the file name, then what is between the
   * last and next to last period is removed, for example:
   *
   *     a.b.c => a.c
   *     a.b.c.d => a.b.d
   *     a.b => a.b
   *     a => a
   *
   * @param $filename
   *   A filename string, for example 'a.b.txt'
   *
   * @return
   *   The name radical of this file, for example a.txt.
   */
  static function Radical($filename) {
    return self::Replace($filename, '\1\3');
  }

  /**
   * Returns the attribute of a filename if one exists
   *
   * If >2 periods are present in the file name, then what is between the
   * last and next to last period is kept, for example:
   *
   *     a.b.c => b
   *     a.b.c.d => c
   *     a.b => NULL
   *     a => NULL
   *
   * @param $filename
   *   A filename string, for example 'a.b.txt'
   *
   * @return
   *   Null if there is attribute to extract; otherwise the attribute name, for example
   *   "b".
   */
  static function Attribute($filename) {
    $replaced = self::Replace($filename, '\2');
    if ($replaced != $filename) {
      return $replaced;
    }
    else {
      return NULL;
    }
  }

  /**
   * Returns part of a filename
   *
   * Helper function which runs a preg replace function on a filename and returns
   * the result
   *
   * @param $filename
   *   A filename, for example a, a.b, a.b.c, a.b.c.d
   * @param $replace
   *   A replacement pattern meant to be passed to preg_replace, where:
   *   \1 = everything before the next-to-last period
   *   \2 = everything between the next-to-last and last periods.
   *   \3 = everything after and including the last period
   *
   * @return
   *   The replaced filename, or the same filename in case of an error or if the
   *   pattern is not found.
   *
   * @throws
   *   Exception
   */
  static function Replace($filename, $replace) {
    return preg_replace('/(^.*)\.([^\.]*)(\.[^\.]*$)/', $replace, $filename);
  }

}
