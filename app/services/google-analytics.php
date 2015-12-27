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
    $ga_account = $this->helper->get_option( 'ga_account' );

    if( count( $raw_accounts->getItems() ) > 0 ) {
      $items = $raw_accounts->getItems();
      $accounts = [];
      foreach( $items as $item ) {
        $id = $item->getId();
        if( $id == $ga_account ) {
          $selected = 'selected';
        } else {
          $selected = false;
        }
        $accounts[] = [
          'id'       => $id,
          'name'     => $item->getName(),
          'selected' => $selected
        ];
      }
      return $accounts;
    } else {
      return false;
    }
  }

  public function get_ga_properties_html( $analytics, $account_id ) {
    $html = '';
    $properties = $this->get_ga_properties( $analytics, $account_id );
    if( $properties ) {
      foreach( $properties as $property ) {
        if( $property['selected'] ) {
          $selected = ' selected="selected"';
        } else {
          $selected = '';
        }
        $html .= '<option value="' . $property['id'] . '"' . $selected . '>'
          . $property['name']
          . '</option>';
      }
    }

    return $html;
  }

  public function get_ga_properties( $analytics, $account_id ) {
    $raw_properties = $analytics->management_webproperties->listManagementWebproperties($account_id);
    $ga_property = $this->helper->get_option( 'ga_property' );

    if( count( $raw_properties->getItems() ) > 0 ) {
      $items = $raw_properties->getItems();
      $properties = [];
      foreach( $items as $item ) {
        $id = $item->getId();
        if( $id == $ga_property ) {
          $selected = 'selected';
        } else {
          $selected = false;
        }
        $properties[] = [
          'id'       => $item->getId(),
          'name'     => $item->getName(),
          'selected' => $selected
        ];
      }

      return $properties;
    } else {
      return false;
    }
  }
}
