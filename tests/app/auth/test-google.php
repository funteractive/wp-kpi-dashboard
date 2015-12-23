<?php

class GoogleTest extends WP_UnitTestCase {

  public function setUp() {
    parent::setUp();
    $this->google = new WpKpiDashboard_Google();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function test_template_setup() {

  }
}
