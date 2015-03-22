<?php

/**
 * @file
 *
 * Define RealisticDummyContentFileGroup autoload class.
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
class RealisticDummyContentFileGroup {

  private $radical;
  private $file;
  private $attributes;

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
   * @param $radical
   *   The radical file name, which may or may not exist on the filesystem.
   *   For example, if the file is called a.b.c, the radical is a.c, even though
   *   a.c does not exist on the filesystem.
   * @param $file
   *   The radical drupal file object, or NULL if it does not exist on the file system.
   * @param $attributes
   *   An array in the format:
   *    array(
   *     'format' => [file object],
   *   ),
   *
   * @throws
   *   RealisticDummyContentException
   */
  function __construct($radical, $file, $attributes) {
    if (!is_string($radical)) {
      throw new RealisticDummyContentException('Please use string for radical');
    }
    if ($file && !is_object($file)) {
      throw new RealisticDummyContentException('Please use NULL or object for file');
    }
    if (!is_array($attributes)) {
      throw new RealisticDummyContentException('Please use array for attributes');
    }
    $this->radical = $radical;
    $this->file = $file;
    $this->attributes = $attributes;
  }

  /**
   * Returns the value of the radical file if one exists.
   *
   * @return
   *   NULL if a radical file does not exist, if it does not have contents, or
   *   if an error occurred. Otherwise returns the contents of the file.
   */
  function Value() {
    try {
      $radical = $this->GetRadical();
      if (isset($radical->uri)) {
        return trim(RealisticDummyContentEnvironment::Get()->file_get_contents($radical->uri));
      }
      else {
        return NULL;
      }
    }
    catch (Exception $e) {
      return NULL;
    }
  }

  function File() {
    $radical = $this->GetRadical();
    if (!$radical) {
      return FALSE;
    }
    else {
      return self::GetFileContents($radical);
    }
  }

  /**
   * Return an attribute name if possible
   *
   * @return
   *   $this->attributes.
   */
  function Attribute($name) {
    return $this->attributes;
  }

  /**
   * Returns the value of an attribute.
   *
   * @param $name
   *   The name of the attribute, for example, if the file is called a.b.c,
   *   then the attribute b will contain what is in that file.
   *
   * @return
   *   NULL if a radical file does not exist, or if an error occurred.
   */
  function GetAttribute($name) {
    try {
      $attributes = $this->GetAttributes();
      if (isset($attributes[$name])) {
        $file = $attributes[$name];
        return trim(RealisticDummyContentEnvironment::Get()->file_get_contents($file->uri));
      }
      else {
        return NULL;
      }
    }
    catch (Exception $e) {
      return NULL;
    }
  }

  /**
   * Returns the extension of the radical filename.
   *
   * @return
   *   An extension, can be empty.
   */
  function GetRadicalExtension() {
    $filename = $this->GetRadical();
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    return $extension;
  }

  function GetRadical() {
    return $this->radical;
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
   *
   * @throws
   *   RealisticDummyContentException
   */
  static function Radical($filename) {
    if (!is_string($filename)) {
      throw new RealisticDummyContentException('Please pass ' . __FUNCTION__ . ' a string as a filename, not a ' . gettype($filename));
    }
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
   *
   * @throws
   *   Exception
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

}
