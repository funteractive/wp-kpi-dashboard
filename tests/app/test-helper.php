<?php

class HelperTest extends WP_UnitTestCase {

  public function setUp() {
    parent::setUp();
    $this->helper = new WpKpiDashboard_Helper();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function test__() {
    $this->assertEquals(
      $this->helper->_( 'hoge' ),
      'hoge'
    );
  }

  public function test_e() {
    $this->expectOutputString('hoge');
    $this->helper->e('hoge');
  }
}
