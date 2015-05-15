<?php
/**
 * @file
 * This file contains the testing code for this module
 */

namespace Drupal\Tests\realistic_dummy_content_api\Unit;
use Drupal\Tests\UnitTestCase;

/**
 * Test pure functions for Realistic dummy content.
 *
 * @group phpunit_realistic_dummy_content
 * @group realistic_dummy_content
 */
class RealisticDummyContentUnitTestCase extends UnitTestCase {
  public function setUp() {
    // specifically include files which contain functions to test.
    require_once 'modules/realistic_dummy_content/api/realistic_dummy_content_api.module';
    parent::setUp();
  }

  /*
   * Test case for realistic_dummy_content_api.
   */
  public function testModule() {
    // only pure functions should be tested here. The database is not available.
    $user = (object)array();
    $user->mail = 'whatever@example.com.invalid';
    $this->assertTrue(realistic_dummy_content_api_realistic_dummy_content_api_dummy($user, 'user'), 'User with an email ending in .invalid is considered dummy content');

    $user->mail = 'whatever@example.com';
    $user->devel_generate = TRUE;
$this->assertTrue(realistic_dummy_content_api_realistic_dummy_content_api_dummy($user, 'user'), 'User with the devel_generate property set is considered dummy content');

    unset($user->devel_generate);
$this->assertFalse(realistic_dummy_content_api_realistic_dummy_content_api_dummy($user, 'user'), 'User with neither an address ending in .invalid nor the devel_generate property set is considered non-dummy');

    $node = (object)array();
    $node->devel_generate = array();
$this->assertTrue(realistic_dummy_content_api_realistic_dummy_content_api_dummy($node, 'node'), 'Node with the devel_generate property set to an empty array is considered dummy');

    unset($node->devel_generate);
$this->assertFALSE(realistic_dummy_content_api_realistic_dummy_content_api_dummy($node, 'node'), 'Node with the devel_generate not set is considered non-dummy');

    $this->assertSequential(0, 3, 'a', 0);
    $this->assertSequential(0, 3, 'a', 0);
    $this->assertSequential(0, 3, 'b', 1);
    $this->assertSequential(0, 3, 'b', 1);
    $this->assertSequential(0, 3, 'c', 2);
    $this->assertSequential(0, 3, 'c', 2);
    $this->assertSequential(0, 3, 'd', 3);
    $this->assertSequential(0, 2, 'd', 2);
    $this->assertSequential(10, 13, 'd', 10);
    $this->assertSequential(11, 12, 'd', 11);
  }

  /**
   * Helper function to assert that the sequential number generator works.
   *
   * This calls realistic_dummy_content_api_sequential(), making sure that the result
   * is as expected.
   *
   * @param $start
   *   Start number passed to realistic_dummy_content_api_sequential()
   * @param $end
   *   End number passed to realistic_dummy_content_api_sequential()
   * @param $hash
   *   Hash passed to realistic_dummy_content_api_sequential()
   * @param $expected
   *   Expected result which realistic_dummy_content_api_sequential() is expected
   *   to return.
   */
  function assertSequential($start, $end, $hash, $expected) {
    $result = realistic_dummy_content_api_sequential($start, $end, $hash);
    $this->assertTrue($result == $expected, 'Sequential number is as expected for ' . $start . ', ' . $end . ' with hash ' . $hash . ': [expected] ' . $expected . ' = [result] ' . $result);
  }

  /**
   * Test that file names are properly parsed and combined.
   */
  function testFiles() {
    require_once 'modules/realistic_dummy_content/api/src/\Drupal\realistic_dummy_content_api\RealisticDummyContentEnvironment.php';
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
      $parsed = \Drupal\realistic_dummy_content_api\RealisticDummyContentEnvironment::SortCandidateFiles($data);
      $parsed_images = \Drupal\realistic_dummy_content_api\RealisticDummyContentEnvironment::SortCandidateFiles($data, array('png'));
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

  /**
   * Test that empty files and non-existing files are treated differently.
   */
  function testEmpty() {
    require_once 'modules/realistic_dummy_content/api/src/\Drupal\realistic_dummy_content_api\RealisticDummyContentAttribute.php';
    require_once 'modules/realistic_dummy_content/api/src/\Drupal\realistic_dummy_content_api\RealisticDummyContentField.php';
    require_once 'modules/realistic_dummy_content/api/src/\Drupal\realistic_dummy_content_api\RealisticDummyContentValueField.php';
    require_once 'modules/realistic_dummy_content/api/src/\Drupal\realistic_dummy_content_api\RealisticDummyContentUnitTestCaseDummyFile.php';
    $field = new \Drupal\realistic_dummy_content_api\RealisticDummyContentValueField('ignore entity', 'ignore name');
    $null = new \Drupal\realistic_dummy_content_api\RealisticDummyContentUnitTestCaseDummyFile(NULL);
    $empty = new \Drupal\realistic_dummy_content_api\RealisticDummyContentUnitTestCaseDummyFile('');

    $this->assertFalse(is_array($field->ValueFromFile_($null)), 'No applicable field value is represented by NULL.');
    $this->assertTrue(is_array($field->ValueFromFile_($empty)), 'An empty string is considered a valid value.');
  }
}
