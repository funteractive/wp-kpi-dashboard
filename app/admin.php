<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

/**
 * Class WpApiAuth_Admin
 */
class WpKpiDashboard_Admin
{

  public $save_action = 'wp-kpi-dashboard-save';

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

  public function setup() {
    var_dump($_POST);
    // When save
    if( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], $this->save_action ) ) {
      if( !isset( $_POST['year'] ) || !$_POST['year'] )
        new WP_Error( 'error', $this->helper->_( 'Year is not set.' ) );

      $this->update_option( 'pv' );
    } else {

    }
  }

  /**
   * Load scripts for admin.
   */
  public function admin_script() {
    wp_enqueue_script( 'wp_kpi_dashboard_admin', WP_KPI_DASHBOARD_URL . 'assets/js/admin.js' );
  }

  /**
   * Template include helper.
   *
   * @param $name
   */
  public static function get_template( $name ) {
    $path = WP_KPI_DASHBOARD_DIR . 'app/templates/' . $name . '.php';
    if( file_exists( $path ) ){
      include $path;
    }
  }

  private function save_option( $option_key, $value ) {
    $option_name = $this->option_name[$option_key];
    var_dump($value);
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
    $value = $this->get_option( $option_key );
    $update_value = [];
    $year = esc_html( $_POST['year'] );

    for( $month = 1; $month <= 12; $month++ ) {
      $update_value[] = esc_html( $_POST["month_{$month}"] );
    }
    $value[$year] = $update_value;
    $this->save_option( $option_key, $value );
  }
}