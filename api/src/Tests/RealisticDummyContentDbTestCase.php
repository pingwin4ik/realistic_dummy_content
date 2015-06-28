<?php
/**
 * @file
 *
 * This file contains the testing code which requires the database. These
 * tests are slower than the unit tests so we want to limit them.
 */

namespace Drupal\realistic_dummy_content_api\Tests;

use Drupal\realistic_dummy_content_api\facade\RealisticDummyContent;
use Drupal\realistic_dummy_content_api\SimpleGenerator;
use Drupal\simpletest\WebTestBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\realistic_dummy_content_api\loggers\DebugLog;
use Drupal\realistic_dummy_content_api\attributes\Field;
use Drupal\realistic_dummy_content_api\manipulators\FieldModifier;

/**
 * Test Realistic dummy content with a temporary database.
 *
 * @group realistic_dummy_content
 */
class RealisticDummyContentDbTestCase extends WebTestBase {
  // Adding 'filter' because of https://www.drupal.org/node/2487786
  public static $modules = array('realistic_dummy_content_api', 'realistic_dummy_content', 'devel_generate', 'filter');

  // Standard, because we need the article node type.
  public $profile = 'standard';

  /**
   * Enable the module
   */
  public function setUp() {
    parent::setUp();

    // Our test needs to check that a given content was replaced, so turn
    // off random number generator during the test.
    \Drupal::state()->set('realistic_dummy_content_api_rand', RealisticDummyContent::REALISTIC_DUMMY_CONTENT_SEQUENTIAL);
  }

  /*
   * Test case for creating a node.
   */
  public function testNode() {
    // Create a node with the devel_generate property set.
    $nids = array();
    for ($i = 1; $i <= 9; $i++) {
      $node_values = array(
        'title' => $this->randomString(),
        'type' => 'article',
        'body' => array(
          \Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED => array(
            array(
              $this->randomString(), // This should always be replaced.
            ),
          ),
        ),
      );
      $node_values['devel_generate'] = array('whatever');
      $node = entity_create('node', $node_values);
      $node->save();
      $nids[$node->id()] = $node->id();
    }

    // Load the nodes
    $nodes = Node::loadMultiple($nids);

    $expected_values = array(
      'title' => array(
        'F',
        'S',
        'T',
      ),
      'body' => array(
        'D',
        'I',
        NULL,
      ),
      'tag' => array(
        'people',
        'historical',
        'events',
      ),
    );

    $images_with_alt = array();
    foreach ($nodes as $nid => $node) {
      // The node should have replaced the image with our own.
      $image_set = isset($node->field_image[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED]);
      $this->assertTrue($image_set, 'Node image is set. There exists an image for this node because $node->field_image[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED] is not empty.');
      if (!$image_set) {
        continue;
      }
      $filename = $node->field_image[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['filename'];
      $this->assertTrue(Unicode::substr($filename, 0, Unicode::strlen('dummyfile')) == 'dummyfile', 'The image file was replaced as expected for node/article/field_image. We know this because the filename starts with the string "dummyfile"');
      if (isset($node->field_image[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['alt'])) {
        $images_with_alt[$node->id()] = $node->field_image[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['alt'];
      }

      $title = $node->title[0];
      $title_expected = $expected_values['title'][($nid - 1)%3];
      $body_expected = $expected_values['body'][($nid - 1)%count($expected_values['body'])];
      if ($body_expected == 'I') {
        $body_format = $node->body[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['format'];
        $this->assertTrue($body_format == 'full_html', 'Using full html for node ' . $nid . ' as expected (using ' . $body_format . ').');
      }
      elseif ($body_expected) {
        $this->assertTrue($node->body[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['format'] == 'filtered_html', 'Using filtered html for node ' . $nid . ' as expected.');
      }
      $this->assertTrue($title == $title_expected, 'Title first letter (' . $title . ') is as expected (' . $title_expected . ') for this nid (' . $nid . ')');
      if ($body_expected === NULL) {
        $this->assertTrue($node->body == array(), 'Body is not set because we have an empty file.');
      }
      else {
        $this->assertTrue($node->body[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['value'][0] == $body_expected, 'Body first letter (' . $node->body[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['value'][0] . ') is as expected (' . $body_expected . ') for this nid (' . $nid . ')');
      }
      $this->assertTag($expected_values['tag'][($nid - 1)%3], $node);
    }
    $this->assertTrue(count($images_with_alt) == 3, 'Three images are expected to have an alt because even though several images exist, we are using the sequential method and therefore only cycling through the first three (because there are only three values to other fields); ' . count($images_with_alt) . ' have an alt text; ' . (count($nodes) - count($images_with_alt)) . ' do not have an alt text');
  }

}
