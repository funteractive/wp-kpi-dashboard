<?php
/*
Plugin Name: WP KPI Dashboard
Version: 0.1.0
Description: Display your kpi and current stats on dashboard.
Author: Keisuke Imura
Author URI: https://funteractive.co.jp/
Plugin URI: https://funteractive.co.jp/
Text Domain: wp-kpi-dashboard
Domain Path: /languages
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

/**
 * Plugin version
 *
 * @const string
 */
if( !defined( 'WP_KPI_DASHBOARD_VERSION' ) )
  define('WP_KPI_DASHBOARD_VERSION', '0.1.0');

new WpKpiDashboard();

/**
 * Class WpKpiDashboard
 */
class WpKpiDashboard
{

  /**
   * WpApiAuth constructor.
   */
  public function __construct(){
    $this->setup();
  }

  /**
   * Setup plugin basic settings.
   */
  private function setup() {
    // Plugin Path
    if ( !defined( 'WP_KPI_DASHBOARD_DIR' ) ) {
      define( 'WP_KPI_DASHBOARD_DIR', plugin_dir_path( __FILE__ ) );
    }

    // Plugin URL
    if ( !defined( 'WP_KPI_DASHBOARD_URL' ) ) {
      define( 'WP_KPI_DASHBOARD_URL', plugins_url( '/', __FILE__ ) );
    }

    // Plugin Domain
    if( !defined( 'WP_KPI_DASHBOARD_DOMAIN' ) ) {
      define( 'WP_KPI_DASHBOARD_DOMAIN', 'wp-kpi-dashboard' );
    }

    // Option Page
    require_once( WP_KPI_DASHBOARD_DIR . 'admin/admin.php' );
    $this->admin = new WpKpiDashboard_Admin();
  }
}
