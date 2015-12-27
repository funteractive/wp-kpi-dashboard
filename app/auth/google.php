<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

/**
 * Class WpKpiDashboard_Google
 */
class WpKpiDashboard_Google
{

  protected $secrets_key = [ 'client_id', 'client_secret' ];

  public function __construct() {
    // Include helper class.
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpKpiDashboard_Helper();

    // Include GA class.
    require_once( WP_KPI_DASHBOARD_DIR . 'app/services/google-analytics.php' );
    $this->ga = new WpKpiDashboard_Google_Analytics();

    $this->init();
  }

  /**
   * @return array
   */
  public function template_get_google_data() {
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

  /**
   * @return bool|string
   * @throws Exception
   */
  public function template_get_gadata() {
    if( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
      $data = [];

      $ga_accounts = $this->ga->get_ga_accounts( $this->analytics );
      if( $ga_accounts ) {
        $data['accounts'] = $ga_accounts;
      }

      return $data;
      //$profile_id = $this->getFirstprofileId( $this->analytics );
      //$results = $this->get_results( $this->analytics, $profile_id );

      //return $this->print_results( $results );
    } else {
      return false;
    }
  }

  /**
   *
   */
  private function init() {
    $json = $this->get_secrets_json();
    if( $json ) {
      $this->set_client( $json );

      if( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
        try {
          $this->client->setAccessToken($_SESSION['access_token']);
          $this->analytics = new Google_Service_Analytics($this->client);
        } catch (Google_Exception $e) {
          $this->refresh();
          echo $e->getMessage();
        }
        if( $this->helper->get_request( 'ajax_ga_account' ) ) {
          echo $this->ga->get_ga_properties_html( $this->analytics, $_POST['ajax_ga_account'] );
          exit();
        }
      }

      if( $this->helper->get_request( 'submit_google' ) ) {
        // When press "Get token" button.
        foreach( $this->secrets_key as $key ) {
          if( isset( $_POST[$key] ) && $_POST[$key] ) {
            $this->helper->save_option( $key, $_POST[$key] );
          }
        }
        $this->redirect_to_auth_url();
      } elseif( $this->helper->get_request( 'submit_ga' ) ) {
        $this->save_ga_account_settings();
      } elseif( isset( $_GET['code'] ) ) {
        // After authenticate and redirect.
        $this->authenticate($_GET['code']);
      } elseif( $this->helper->get_request( 'reset_google' ) ) {
        // When press "Clear Authorization" button.
        $this->reset();
      }
    }
  }

  /**
   * @return string
   */
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

    $redirect_uri = $this->get_redirect_uri();
    header( "Location: $redirect_uri", true, '302' );
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

  private function save_ga_account_settings() {
    if( $this->helper->get_request( 'ga_account' ) ) {
      $this->helper->save_option( 'ga_account', $_POST['ga_account'] );
    }
    if( $this->helper->get_request( 'ga_property' ) ) {
      $this->helper->save_option( 'ga_property', $_POST['ga_property'] );
    }
  }
}
