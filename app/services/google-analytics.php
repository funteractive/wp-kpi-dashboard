<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

/**
 * Class WpKpiDashboard_Google_Analytics
 */
class WpKpiDashboard_Google_Analytics
{

  public function __construct() {
    // Include helper class.
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpKpiDashboard_Helper();
  }

  public function get_ga_accounts( $analytics ) {
    $raw_accounts = $analytics->management_accounts->listManagementAccounts();
    if( count( $raw_accounts->getItems() ) > 0 ) {
      $items = $raw_accounts->getItems();
      $accounts = [];
      foreach( $items as $item ) {
        $accounts[] = [
          'id' => $item->getId(),
          'name' => $item->getName()
        ];
      }

      return $accounts;
    } else {
      return false;
    }
  }

  public function get_ga_properties_html( $analytics, $account_id ) {
    $properties = $this->get_ga_properties( $analytics, $account_id );
  }

  private function get_ga_properties( $analytics, $account_id ) {
    $raw_properties = $analytics->management_webproperties->listManagementWebproperties($account_id);
    if( count( $raw_properties->getItems() ) > 0 ) {
      $items = $raw_properties->getItems();
      $properties = [];
      foreach( $items as $item ) {
        $properties[] = [
          'id' => $item->getId(),
          'name' => $item->getName()
        ];
      }

      return $properties;
    } else {
      return false;
    }
  }
}