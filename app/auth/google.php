<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

class WpKpiDashboard_Google
{

  public function __construct() {
    // Include helper class.
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpApiAuth_Helper();

    // Set client data.
    $this->set_client();

    if( isset( $_GET['code'] ) ) {
      $this->authenticate( $_GET['code'] );
    }
  }

  private function set_client() {
    $protocol = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';

    $this->client = new Google_Client();
    $this->client->setAuthConfigFile( WP_API_AUTH_DIR . 'client_secrets.json' );
    $this->client->setRedirectUri( $protocol . $_SERVER['HTTP_HOST'] . '/wp-admin/options-general.php?page=wp-kpi-dashboard' );
    $this->client->setScopes( Google_Service_Analytics::ANALYTICS_READONLY );
    $this->client->setAccessType( 'offline' );
    $this->client->setApprovalPrompt( 'force' );
  }
}
