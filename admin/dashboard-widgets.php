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

    // include services
    require_once( WP_KPI_DASHBOARD_DIR . 'app/services/pageview.php' );
    $this->pageview = new WpKpiDashboard_Pageview();

    // Set hooks
    add_action( 'wp_dashboard_setup', array( &$this, 'add_dashboard_widgets' ) );
  }

  public function add_dashboard_widgets() {
    wp_add_dashboard_widget(
      'wp_kpi_dashboard',                     // Widget slug.
      $this->helper->_( 'WP KPI Dashboard' ), // Title.
      array( &$this, 'init_widget' )          // Display function.
    );
  }

  /**
   * Create the function to output the contents of our Dashboard Widget.
   */
  public function init_widget() {

    $html = $this->get_pageview_kpi();

    echo $html;
  }

  private function get_pageview_kpi() {
    $page_view_kpi = $this->pageview->get_kpi();
    $year = date( 'Y' );
    $month = date( 'n' );

    $show_kpi = $page_view_kpi[$year][$month];

    return $show_kpi;
  }
}
