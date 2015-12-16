<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

class WpKpiDashboard_Google
{

  protected $secrets_key = [ 'client_id', 'client_secret' ];

  public function __construct() {
    // Include helper class.
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpKpiDashboard_Helper();

    $this->setup();
  }

  public function template_setup() {
    $data = [];
    $data['redirect_uris'] = $this->get_redirect_uri();
    if( $this->helper->get_request( 'reset_google' ) ) {
      return $data;
    }
    foreach( $this->secrets_key as $key ) {
      $data[$key] = $this->helper->get_request_or_option( $key );
    }

    return $data;
  }

  public function template_get_gadata() {
    if( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
      try {
        $this->client->setAccessToken($_SESSION['access_token']);
        $this->service = new Google_Service_Analytics($this->client);
      } catch (Google_Exception $e) {
        $this->refresh();
        echo $e->getMessage();
      }

      // Get Google Analytics accounts.
      $accounts = $this->service->management_accounts->listManagementAccounts();
    } else {
      return false;
    }
  }

  public function setup() {
    $json = $this->get_secrets_json();
    if( $json ) {
      $this->set_client( $json );
      if( $this->helper->get_request( 'submit_google' ) ) {
        $this->redirect_to_auth_url();
      } elseif( isset( $_GET['code'] ) ) {
        $this->authenticate( $_GET['code'] );
      }

      // When reset.
      if( $this->helper->get_request( 'reset_google' ) ) {
        $this->reset();
      } else {
        foreach( $this->secrets_key as $key ) {
          $option_name = WP_KPI_DASHBOARD_PREFIX . $key;
          if( isset( $_POST[$key] ) && $_POST[$key] ) {
            $this->helper->save_option( $option_name, $_POST[$key] );
          }
        }
      }
    }
  }

  private function get_redirect_uri() {
    $protocol = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
    return $protocol . $_SERVER['HTTP_HOST'] . '/wp-admin/options-general.php?page=' . WP_KPI_DASHBOARD_DOMAIN;
  }

  private function get_secrets_json() {
    foreach( $this->secrets_key as $key ) {
      ${$key} = $this->helper->get_request_or_option( $key );
    }
    if( $client_id && $client_secret ) {
      $json = $this->create_secrets_json( $client_id, $client_secret );
      return $json;
    } else {
      return false;
    }
  }

  private function create_secrets_json( $client_id, $client_secret ) {
    $array = [
      'web' => [
        'client_id'                   => $client_id,
        'auth_uri'                    => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri'                   => 'https://accounts.google.com/o/oauth2/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_secret'               => $client_secret,
        'redirect_uris'               => [ $this->get_redirect_uri() ],
      ]
    ];

    $json = json_encode( $array );
    $json = str_replace( '\/', '/', $json );

    return $json;
  }

  /**
   * @param $json
   */
  private function set_client( $json ) {
    $protocol = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';

    $this->client = new Google_Client();
    $this->client->setAuthConfig( $json );
    $this->client->setRedirectUri( $protocol . $_SERVER['HTTP_HOST'] . '/wp-admin/options-general.php?page=' . WP_KPI_DASHBOARD_DOMAIN );
    $this->client->setScopes( Google_Service_Analytics::ANALYTICS_READONLY );
    $this->client->setAccessType( 'offline' );
    $this->client->setApprovalPrompt( 'force' );
  }

  /**
   *
   */
  private function redirect_to_auth_url() {
    $authUrl = $this->client->createAuthUrl();
    header( "Location: $authUrl", true, '302' );
  }

  /**
   * @param $code
   */
  private function authenticate( $code ) {
    $this->client->authenticate( $code );
    try {
      $access_token = $this->client->getAccessToken();
      if( $access_token ) {
        $this->helper->save_option( WP_KPI_DASHBOARD_PREFIX . 'token', $access_token );
        $_SESSION['access_token'] = $access_token;
      }
    } catch( Google_Exception $e ) {
      echo $e->getMessage();
    }
  }

  /**
   * Refresh access token.
   * @return bool
   */
  private function refresh() {
    $token = $this->helper->get_option( WP_KPI_DASHBOARD_PREFIX . 'token' );
    if( !isset( $token['refresh_token'] ) || !$token['refresh_token'] ) {
      return false;
    }

    $this->client->refreshToken( $token['refresh_token'] );
  }

  /**
   * Revoke all tokens.
   */
  private function reset() {
    // Revoke access token.
    $this->client->revokeToken();

    foreach( $this->secrets_key as $key ) {
      $option_name = WP_KPI_DASHBOARD_PREFIX . $key;
      $this->helper->delete_option( $option_name, $_POST[$key] );
    }

    // Unset access token in session.
    if( isset( $_SESSION['access_token'] ) ) unset( $_SESSION['access_token'] );
  }
}
