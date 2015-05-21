<?php

/**
 * @file
 *
 * Define autoload class.
 */

namespace Drupal\Tests\realistic_dummy_content_api\Unit\attributes;

use Drupal\Tests\UnitTestCase;
use Drupal\realistic_dummy_content_api\attributes\ValueField;
use Drupal\realistic_dummy_content_api\environments\UnitTestCaseDummyFile;

class ValueFieldTest extends UnitTestCase {
  /**
   * Test that empty files and non-existing files are treated differently.
   */
  function testValueFromFile_() {
    $field = new ValueField('ignore entity', 'ignore name');
    $null = new UnitTestCaseDummyFile(NULL);
    $empty = new UnitTestCaseDummyFile('');

    $this->assertFalse(is_array($field->ValueFromFile_($null)), 'No applicable field value is represented by NULL.');
    $this->assertTrue(is_array($field->ValueFromFile_($empty)), 'An empty string is considered a valid value.');
  }

}
