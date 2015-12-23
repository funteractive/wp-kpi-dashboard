<?php

class PageViewTest extends WP_UnitTestCase {

  public function setUp() {
    parent::setUp();
    $this->pageview = new WpKpiDashboard_Pageview();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function test_template_setup() {
    $option_name = 'wp_kpi_dashboard_pv_kpi';
    $this->assertEquals(
      $this->pageview->template_setup( $option_name ),
      $this->pageview->get_kpi( $option_name )
    );
  }
}