<?php

/**
 * @file
 * Contains a recipe for generating realistic dummy content
 */

use Drupal\realistic_dummy_content_api\recipes\Recipe;

class realistic_dummy_content_realistic_dummy_content_recipe extends Recipe {
  function _Run_() {
    // If you want sequential numbering rather than random numbering, use
    // \Drupal::state()->set('realistic_dummy_content_api_rand', \Drupal\realistic_dummy_content_api\facade\RealisticDummyContent::REALISTIC_DUMMY_CONTENT_SEQUENTIAL);

    $this->NewEntities('node', 'page', 4, array('kill' => TRUE));
    $this->NewEntities('node', 'article', 10, array('kill' => TRUE));
  }
}
