<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

/**
 * Class WpApiAuth_Admin
 */
class WpKpiDashboard_Admin
{

  protected $save_action = 'wp-kpi-dashboard-save';

  protected $start_year = 2010;
  protected $end_year = 2030;

  protected $months_name = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
  ];

  protected $option_name = [
    'pv' => 'wp_kpi_dashboard_pv_kpi'
  ];

  /**
   * WpKpiDashboard_Admin constructor.
   */
  public function __construct() {
    // include helper
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpKpiDashboard_Helper();

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

  public function template_setup() {
    // When save
    if( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], $this->save_action ) ) {
      $this->update_option( 'pv' );
    }

    // return default data
    return $this->get_option( 'pv' );
  }

  /**
   * Load scripts for admin.
   */
  public function admin_script() {
    wp_enqueue_script( 'wp_kpi_dashboard_lodash', WP_KPI_DASHBOARD_URL . 'bower_components/lodash/lodash.min.js' );
    wp_enqueue_script( 'wp_kpi_dashboard_admin', WP_KPI_DASHBOARD_URL . 'admin/assets/js/admin.js' );
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

  private function save_option( $option_key, $value ) {
    $option_name = $this->option_name[$option_key];
    if( get_option( $option_name ) ) {
      update_option( $option_name, serialize( $value ) );
    } else {
      add_option( $option_name, serialize( $value ) );
    }
  }

  private function get_option( $option_key ) {
    $option_name = $this->option_name[$option_key];
    if( $value = get_option( $option_name ) ) {
      return unserialize( $value );
    } else {
      return false;
    }
  }

  private function update_option( $option_key ) {
    if( !isset( $_POST['pv_kpi'] ) || !is_array( $_POST['pv_kpi'] ) )
      return false;

    $value = $_POST['pv_kpi'];
    $this->save_option( $option_key, $value );
  }
}