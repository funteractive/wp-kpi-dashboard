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

  public function test_get_request() {
    $key = 'key';
    $this->assertFalse(
      $this->helper->get_request( $key )
    );

    $_POST[$key] = $key;
    $this->assertEquals(
      $this->helper->get_request( $key ),
      $key
    );
  }

  public function test_get_option() {
    $key = 'key';
    $value = 'hoge';
    $this->assertFalse(
      $this->helper->get_option( $key )
    );

    add_option( WP_KPI_DASHBOARD_PREFIX . $key, $value );
    $this->assertEquals(
      $this->helper->get_option( $key ),
      $value
    );
  }

  public function test_save_option() {
    $key = 'key';
    $value = 'hoge';
    $this->assertFalse(
      get_option( $key )
    );

    $this->helper->save_option( $key, $value );
    $this->assertEquals(
      get_option( $key ),
      $value
    );
  }

  public function test_delete_option() {
    $key = 'key';
    $value = 'hoge';
    $this->helper->save_option( $key, $value );
    $this->helper->delete_option( $key );
    $this->assertFalse(
      get_option( $key )
    );
  }
}
