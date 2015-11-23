<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();


class WpKpiDashboard_Widgets
{

  public function __construct() {
    // include helper
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpKpiDashboard_Helper();

    // Set hooks
    add_action( 'wp_dashboard_setup', array( &$this, 'add_dashboard_widgets' ) );
  }

  public function add_dashboard_widgets() {
    wp_add_dashboard_widget(
      'wp_kpi_dashboard',         // Widget slug.
      $this->helper->_( 'WP KPI Dashboard' ),         // Title.
      array( &$this, 'example_dashboard_widget_function' ) // Display function.
    );
  }

  /**
   * Create the function to output the contents of our Dashboard Widget.
   */
  function example_dashboard_widget_function() {

    // Display whatever it is you want to show.
    echo "Hello World, I'm a great Dashboard Widget";
  }
}
