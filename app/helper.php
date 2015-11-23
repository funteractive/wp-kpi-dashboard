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
}