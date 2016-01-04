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

  /**
   * WpKpiDashboard_Google constructor.
   */
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

      $ga_account = $this->helper->get_option( 'ga_account' );
      $ga_property = $this->helper->get_option( 'ga_property' );
      if( $ga_account ) {
        $data['properties'] = $this->ga->get_ga_properties( $this->analytics, $ga_account );
      }
      if( $ga_property ) {
        $data['profiles'] = $this->ga->get_ga_profiles( $this->analytics, $ga_account, $ga_property );
      }

      return $data;
    } else {
      return false;
    }
  }

  /**
   * @param $start_date
   * @param $end_date
   * @return bool
   */
  public function dashboard_get_gadata( $start_date, $end_date ) {
    if( isset( $this->analytics ) && $this->analytics ) {
      $pagevies = $this->ga->get_pageviews( $this->analytics, $start_date, $end_date );
      return $pagevies;
    } else {
      return false;
    }
  }

  /**
   * Initialize.
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
          if( $this->helper->get_request( 'ajax_ga_property' ) ) {
            echo $this->ga->get_ga_profiles_html( $this->analytics, $_POST['ajax_ga_account'], $_POST['ajax_ga_property'] );
          } else {
            echo $this->ga->get_ga_properties_html( $this->analytics, $_POST['ajax_ga_account'] );
          }
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
        // When press "Save" in Account Settings button.
        $this->save_ga_account_settings();
      } elseif( isset( $_GET['code'] ) ) {
        // After authenticate and redirect.
        $this->authenticate($_GET['code']);
      } elseif( $this->helper->get_request( 'reset_google' ) ) {
        // When press "Clear Authorization" button.
        $this->reset();
      } elseif( $this->helper->get_request( 'reset_ga' ) ) {
        // When press "Clear Account" button.
        $this->reset_account();
      }
    }
  }

  /**
   * Get url to Google redirect uri.
   * @return string
   */
  private function get_redirect_uri() {
    $protocol = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
    return $protocol . $_SERVER['HTTP_HOST'] . '/wp-admin/options-general.php?page=' . WP_KPI_DASHBOARD_DOMAIN;
  }

  /**
   * Get json for set configures for client.
   * @return bool|mixed|string|void
   */
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

  /**
   * Create json for set configures for client.
   * @param $client_id
   * @param $client_secret
   * @return mixed|string|void
   */
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
   * Redirect to Google authentication url.
   */
  private function redirect_to_auth_url() {
    $authUrl = $this->client->createAuthUrl();
    header( "Location: $authUrl", true, '302' );
  }

  /**
   * Authenticate method.
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

    $this->reset_account();
    foreach( $this->secrets_key as $key ) {
      $this->helper->delete_option( $key );
    }

    // Unset access token in session.
    if( isset( $_SESSION['access_token'] ) ) unset( $_SESSION['access_token'] );
  }

  /**
   * Reset account settings.
   */
  private function reset_account() {
    $account_options = [ 'ga_account', 'ga_property' ];
    foreach( $account_options as $key ) {
      $this->helper->delete_option( $key );
    }
  }

  /**
   * Save account settings.
   */
  private function save_ga_account_settings() {
    if( $this->helper->get_request( 'ga_account' ) ) {
      $this->helper->save_option( 'ga_account', $_POST['ga_account'] );
    }
    if( $this->helper->get_request( 'ga_property' ) ) {
      $this->helper->save_option( 'ga_property', $_POST['ga_property'] );
    }
    if( $this->helper->get_request( 'ga_profile' ) ) {
      $this->helper->save_option( 'ga_profile', $_POST['ga_profile'] );
    }
  }
}
