<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

/**
 * Class WpKpiDashboard_Pageview
 */
class WpKpiDashboard_Pageview
{
  public $save_action = 'wp-kpi-dashboard-save';

  public $start_year = 2010;
  public $end_year = 2030;

  public $months_name = [
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

  protected $option_name = 'wp_kpi_dashboard_pv_kpi';

  public function __construct() {
    // include helper
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpKpiDashboard_Helper();
  }

  public function template_setup() {
    // When save
    if( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], $this->save_action ) ) {
      $this->update_option( $this->option_name );
    }

    // return default data
    return $this->get_option( $this->option_name );
  }

  public function get_kpi() {
    return $this->get_option( $this->option_name );
  }

  private function save_option( $option_name, $value ) {
    if( get_option( $option_name ) ) {
      update_option( $option_name, serialize( $value ) );
    } else {
      add_option( $option_name, serialize( $value ) );
    }
  }

  private function get_option( $option_name ) {
    if( $value = get_option( $option_name ) ) {
      return unserialize( $value );
    } else {
      return false;
    }
  }

  private function update_option( $option_name ) {
    if( !isset( $_POST['pv_kpi'] ) || !is_array( $_POST['pv_kpi'] ) )
      return false;

    $value = $_POST['pv_kpi'];
    $this->save_option( $option_name, $value );
  }
}
