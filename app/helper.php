<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

class WpKpiDashboard_Helper
{
  public function _( $string ) {
    return __( $string, WP_KPI_DASHBOARD_DOMAIN );
  }

  public function e( $string ) {
    return _e( $string, WP_KPI_DASHBOARD_DOMAIN );
  }

  public function get_request_or_option( $value ) {
    if( $request = $this->get_request( $value ) ) {
      return $request;
    } elseif( $option = $this->get_option( $value ) ) {
      return $option;
    } else {
      return false;
    }
  }

  public function get_request( $key ) {
    if( isset( $_POST[$key] ) && $_POST[$key] ) {
      return esc_html($_POST[$key]);
    } else {
      return false;
    }
  }

  public function get_option( $key ) {
    if( $option = get_option( WP_KPI_DASHBOARD_PREFIX . $key ) ) {
      return esc_html( unserialize( $option ) );
    } else {
      return false;
    }
  }

  public function save_option( $key, $value ) {
    if( get_option( $key ) ) {
      update_option( $key, serialize( $value ) );
    } else {
      add_option( $key, serialize( $value ) );
    }
  }

  public function get_checked_value( $value ) {
    if( isset( $value ) ) {
      return $value;
    } else {
      return false;
    }
  }
}
