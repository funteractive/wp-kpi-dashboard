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

    $kpi = $this->get_pageview_kpi();
    $html = $this->get_kpi_block_html( $kpi, 'Pageview' );

    echo $html;
  }

  private function get_kpi_block_html( $kpi, $title ) {
    $html = <<<EOL
<div class="wpkpi_db_block">
  <p class="wpkpi_db_block_title">{$title}</p>
  <span class="wpkpi_db_value">100</span>
  <span class="wpkpi_db_divider">/</span>
  <span class="wpkpi_db_kpi">{$kpi}</span>
</div>
EOL;

    $html = apply_filters( 'wpkpidb_dashboard_block_html', $html );
    return $html;
  }

  private function get_pageview_kpi() {
    $page_view_kpi = $this->pageview->get_kpi();
    $year = date( 'Y' );
    $month = date( 'n' );

    $show_kpi = $page_view_kpi[$year][$month];

    return $show_kpi;
  }
}
