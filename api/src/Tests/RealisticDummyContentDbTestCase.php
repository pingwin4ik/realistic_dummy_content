<?php
/**
 * @file
 *
 * This file contains the testing code which requires the database. These
 * tests are slower than the unit tests so we want to limit them.
 */

namespace Drupal\realistic_dummy_content_api\Tests;
use Drupal\realistic_dummy_content_api\RealisticDummyContentSimpleGenerator;
use Drupal\simpletest\WebTestBase;

use \Drupal\node\Entity\Node;
use \Drupal\user\Entity\User;

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
    \Drupal::state()->set('realistic_dummy_content_api_rand', REALISTIC_DUMMY_CONTENT_SEQUENTIAL);
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
      debug('entity ' . $i . ' created');
      debug($node);
      $nids[$node->nid] = $node->nid;
    }

    // Load the nodes
    $nodes = Node::loadMultiple($nids);

    debug('all nodes loaded');

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
        $images_with_alt[$node->nid] = $node->field_image[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['alt'];
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

  /**
   * Assert that a node has a specified tag in its field_tags
   *
   * This will make sure the tag exists as the taxonomy term, and that it is referenced
   * in $node's field_tags
   *
   * @param $tag_name
   *   A string
   * @param $node
   *   A node object
   */
  public function assertTag($tag_name, $node) {
    $term = taxonomy_get_term_by_name($tag_name);
    $this->assertTrue($term, 'Term ' . $tag_name . ' exists');
    $referenced_term = taxonomy_term_load($node->field_tags[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['tid']);
    if (isset($referenced_term->name)) {
      $referenced_term = $referenced_term->name;
    }
    else {
      $referenced_term = '[unknown term]';
    }

    $this->assertTrue(array_key_exists($node->field_tags[\Drupal\Core\Language\Language::LANGCODE_NOT_SPECIFIED][0]['tid'], $term), 'Term is referenced in node ' . $node->nid . ' (' . $referenced_term . ' == ' . $tag_name . ')');
  }

  /*
   * Test case for creating a user.
   */
  public function testUser() {
    // Create a user with devel_generate

    $generator = new \Drupal\realistic_dummy_content_api\RealisticDummyContentSimpleGenerator('user', 'user', 1);
    $generator->Generate();

    // Load the user and view
    $user = User::load(2);

    $filename = $user->picture->filename;
    $this->assertTrue(Unicode::substr($filename, 0, Unicode::strlen('dummyfile')) == 'dummyfile', 'The user\'s picture file was replaced as expected. We know this because the filename starts with the string "dummyfile"');
    $current_picture = $user->picture;
    $user->save();
    $user = User::load($user->uid);
    $this->assertTrue($current_picture == $user->picture, 'The dummy file generation happens when the user is first created, not when it is resaved.');
  }

  /*
   * Test case for recipes.
   */
  public function testRecipe() {
    $this->assertTrue(module_load_include('inc', 'realistic_dummy_content_api', 'realistic_dummy_content_api.drush'), 'drush file exists');
    $this->assertTrue(class_exists('\Drupal\realistic_dummy_content_api\RealisticDummyContentDrushAPILog'), 'The drush log class exists; it is required when running drush generate-realistic or other drush commands');
    realistic_dummy_content_api_apply_recipe(new \Drupal\realistic_dummy_content_api\RealisticDummyContentDebugLog);
    $page = Node::load(4);
    $article = Node::load(14);
    $this->assertTrue(isset($page->type) && $page->type == 'page', 'Node 4 is a page, as specified in the recipe.');
    $this->assertTrue(isset($article->type) && $article->type == 'article', 'Node 14 is an article, as specified in the recipe.');
  }

  /*
   * Test that attributes work correctly
   */
  public function testAttributes() {
    $node = $this->drupalCreateNode(array('type' => 'article'));
    $modifier = new \Drupal\realistic_dummy_content_api\RealisticDummyContentFieldModifier($node, 'node');
    $attributes = $modifier->GetAttributes();
    $existing = array();
    foreach ($attributes as $index => $attribute) {
      $name = $attribute->GetName();
      $this->assertFalse(in_array($name, $existing), 'Attribute ' . $name . ' only has one modifier');
      $existing[] = $name;
    }
  }

}
