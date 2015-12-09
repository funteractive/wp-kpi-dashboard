<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

/**
 * Class WpApiAuth_Admin
 */
class WpKpiDashboard_Admin
{

  /**
   * WpKpiDashboard_Admin constructor.
   */
  public function __construct() {
    // include helper
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpKpiDashboard_Helper();

    // include auth
    require_once( WP_KPI_DASHBOARD_DIR . 'app/auth/google.php' );
    $this->google = new WpKpiDashboard_Google();

    // include services
    require_once( WP_KPI_DASHBOARD_DIR . 'app/services/pageview.php' );
    $this->pageview = new WpKpiDashboard_Pageview();

    // Set hooks
    add_action( 'admin_menu',            array( &$this, 'admin_menu' ) );
    add_action( 'admin_enqueue_scripts', array( &$this, 'admin_script' ) );
  }

  /**
   * Add API Setting menu in manage options menu.
   */
  public function admin_menu() {
    add_options_page(
      $this->helper->_( 'WP KPI Dashboard' ),
      $this->helper->_( 'WP KPI Dashboard' ),
      'manage_options',
      WP_KPI_DASHBOARD_DOMAIN,
      array( $this, 'render_admin_page' )
    );
  }

  /**
   * Get admin page template.
   */
  public function render_admin_page() {
    $this->get_template( 'general' );
  }

  /**
   * Load scripts for admin.
   */
  public function admin_script() {
    if( is_admin() ) {
      wp_enqueue_script( 'wp_kpi_dashboard_lodash', WP_KPI_DASHBOARD_URL . 'bower_components/lodash/lodash.min.js' );
      wp_enqueue_script( 'wp_kpi_dashboard_admin', WP_KPI_DASHBOARD_URL . 'admin/assets/js/admin.js' );
    }
  }

  /**
   * Template include helper.
   *
   * @param $name
   */
  public static function get_template( $name ) {
    $path = WP_KPI_DASHBOARD_DIR . 'admin/templates/' . $name . '.php';
    if( file_exists( $path ) ){
      include $path;
    }
  }

}