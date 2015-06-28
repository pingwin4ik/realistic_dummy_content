<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\Tests\realistic_dummy_content_api\Unit\facade;

use Drupal\Tests\UnitTestCase;
use Drupal\realistic_dummy_content_api\facade\RealisticDummyContent;

/**
 * Test Realistic dummy content.
 *
 * @group realistic_dummy_content
 */
class RealisticDummyContentTest extends UnitTestCase {
  function testIsDummy() {
    // only pure functions should be tested here. The database is not available.
    $user = (object)array();
    $user->mail = 'whatever@example.com.invalid';
    $this->assertTrue(RealisticDummyContent::realistic_dummy_content_api_dummy($user, 'user'), 'User with an email ending in .invalid is considered dummy content');

    $user->mail = 'whatever@example.com';
    $user->devel_generate = TRUE;
$this->assertTrue(RealisticDummyContent::realistic_dummy_content_api_dummy($user, 'user'), 'User with the devel_generate property set is considered dummy content');

    unset($user->devel_generate);
$this->assertFalse(RealisticDummyContent::realistic_dummy_content_api_dummy($user, 'user'), 'User with neither an address ending in .invalid nor the devel_generate property set is considered non-dummy');

    $node = (object)array();
    $node->devel_generate = array();
$this->assertTrue(RealisticDummyContent::realistic_dummy_content_api_dummy($node, 'node'), 'Node with the devel_generate property set to an empty array is considered dummy');

    unset($node->devel_generate);
$this->assertFALSE(RealisticDummyContent::realistic_dummy_content_api_dummy($node, 'node'), 'Node with the devel_generate not set is considered non-dummy');
  }

  /*
   * Test case for realistic_dummy_content_api.
   */
  public function testSequential() {
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
    $result = RealisticDummyContent::sequential($start, $end, $hash);
    $this->assertTrue($result == $expected, 'Sequential number is as expected for ' . $start . ', ' . $end . ' with hash ' . $hash . ': [expected] ' . $expected . ' = [result] ' . $result);
  }

}
