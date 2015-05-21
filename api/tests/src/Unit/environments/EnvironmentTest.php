<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\Tests\realistic_dummy_content_api\Unit\environments;

use Drupal\Tests\UnitTestCase;

use Drupal\realistic_dummy_content_api\environments\Environment;

class EnvironmentTest extends UnitTestCase {
  /**
   * Test that file names are properly parsed and combined.
   */
  function testSortCandidateFiles() {
    $data = array(
      'one.txt' => (object) array(),
      'reAdme.txt' => (object) array(),
      'README.md' => (object) array(),
      'readme.jpg' => (object) array(),
      'two.txt' => (object) array(),
      'two.notanattribute.txt' => (object) array(),
      'two.txt.attribute.txt' => (object) array(),
      'two.txt.attribute1.txt' => (object) array(),
      'three.png' => (object) array(),
      'three.png.alt.txt' => (object) array(),
    );
    try {
      $parsed = Environment::SortCandidateFiles($data);
      $parsed_images = Environment::SortCandidateFiles($data, array('png'));
    }
    catch (\Exception $e) {
      $this->assertFalse(TRUE, 'Got \Exception ' . $e->getMessage());
    }
    $this->assertTrue(count($parsed) == 4, '4 parsed files are returned, which excludes the readme riles (4 == ' . count($parsed) . ')');
    $this->assertTrue(is_object($parsed['one.txt']['file']));
    $this->assertTrue(is_object($parsed['two.txt']['file']));
    $this->assertTrue(is_object($parsed['two.txt']['attributes']['attribute']));
    $this->assertTrue(is_object($parsed['two.txt']['attributes']['attribute1']));
    $this->assertTrue(is_object($parsed['three.png']['file']));
    $this->assertTrue(is_object($parsed['three.png']['attributes']['alt']));
    $this->assertFalse(isset($parsed_images['two.txt']['attributes']['attribute1']));
    $this->assertTrue(is_object($parsed_images['three.png']['file']));
    $this->assertTrue(is_object($parsed_images['three.png']['attributes']['alt']));
    $this->assertTrue(is_object($parsed['two.notanattribute.txt']['file']));
  }

}
