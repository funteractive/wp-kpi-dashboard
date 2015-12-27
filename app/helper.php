<?php
// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

/**
 * Class WpKpiDashboard_Helper
 */
class WpKpiDashboard_Helper
{
  /**
   * @param $string
   * @return string|void
   */
  public function _( $string ) {
    return __( $string, WP_KPI_DASHBOARD_DOMAIN );
  }

  /**
   * @param $string
   */
  public function e( $string ) {
    return _e( $string, WP_KPI_DASHBOARD_DOMAIN );
  }

  /**
   * @param $value
   * @return bool|string
   */
  public function get_request_or_option( $value ) {
    if( $request = $this->get_request( $value ) ) {
      return $request;
    } elseif( $option = $this->get_option( $value ) ) {
      return $option;
    } else {
      return false;
    }
  }

  /**
   * @param $key
   * @return bool|string
   */
  public function get_request( $key ) {
    if( isset( $_POST[$key] ) && $_POST[$key] ) {
      return esc_html($_POST[$key]);
    } else {
      return false;
    }
  }

  /**
   * @param $key
   * @return bool|string
   */
  public function get_option( $key ) {
    if( $option = get_option( WP_KPI_DASHBOARD_PREFIX . $key ) ) {
      return esc_html( $option );
    } else {
      return false;
    }
  }

  /**
   * @param $key
   * @param $value
   */
  public function save_option( $key, $value ) {
    $key = WP_KPI_DASHBOARD_PREFIX . $key;
    if( get_option( $key ) ) {
      update_option( $key, $value );
    } else {
      add_option( $key, $value );
    }
  }

  /**
   * @param $key
   */
  public function delete_option( $key ) {
    delete_option( $key );
  }
}
