<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

// Include Admin Class
require_once( WP_KPI_DASHBOARD_DIR . 'admin/admin.php' );
$admin = new WpKpiDashboard_Admin();

// Include Google Class
require_once( WP_KPI_DASHBOARD_DIR . 'app/auth/google.php' );
$google = new WpKpiDashboard_Google();

// Include Pageview Class
require_once( WP_KPI_DASHBOARD_DIR . 'app/services/pageview.php' );
$pageview = new WpKpiDashboard_Pageview();

// Include Helper Class
require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
$helper = new WpKpiDashboard_Helper();

// setup
$google_datas = $google->template_setup();
$pageview_datas = $pageview->template_setup();

// months name
$months_name = $pageview->months_name;

// create nonce
$nonce = wp_create_nonce( $pageview->save_action );

// default year
$start_year = $pageview->start_year;
$end_year = $pageview->end_year;
$default_year = date( 'Y' );
?>

<div class="wrap">
  <h2><?php $helper->e( 'WP KPI Dashboard' ); ?></h2>

  <h3><?php $helper->e( 'Google Analytics Settings' ); ?></h3>
  <form action="<?php echo admin_url( 'options-general.php?page=' . WP_KPI_DASHBOARD_DOMAIN ); ?>" method="POST">
    <table class="form-table">
      <tbody>
      <tr>
        <th><?php $helper->e( 'Client ID' ); ?></th>
        <td>
          <input type="text" class="regular-text" name="client_id">
        </td>
      </tr>
      <tr>
        <th><?php $helper->e( 'Consumer secret key' ); ?></th>
        <td>
          <input type="text" class="regular-text" name="client_secret">
        </td>
      </tr>
      <tr>
        <th><?php $helper->e( 'Redirect URI' ); ?></th>
        <td>
          <input type="text" class="regular-text" name="redirect_uris" value="<?php echo $google_datas['redirect_uris']; ?>" readonly>
        </td>
      </tr>
      <tr>
        <th><?php $helper->e( 'Token' ); ?></th>
        <td><pre></pre></td>
      </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>">
      <input type="submit" name="reset_google" class="button button-secondary" value="<?php $helper->e( 'Clear Authorization' ); ?>" />
      <input type="submit" name="submit_google" id="submit" class="button button-primary" value="<?php $helper->e( 'Get token' ); ?>">
    </p>
  </form>
  <hr>
  <h3><?php $helper->e( 'Page View Settings' ); ?></h3>
  <select name="year" id="js-wpkpidb-years-select">
    <?php for( $year = $start_year; $year <= $end_year; $year++ ):
      $selected = '';
      if( $year == $default_year )
        $selected = ' selected';
      ?>
      <option value="<?php echo esc_attr( $year ); ?>"<?php echo $selected; ?>>
        <?php echo esc_html( $year ); ?>
      </option>
    <?php endfor; ?>
  </select>

  <form action="<?php echo admin_url( 'options-general.php?page=' . WP_KPI_DASHBOARD_DOMAIN ); ?>" method="POST">
    <?php for( $year = $start_year; $year <= $end_year; $year++ ): ?>
      <table class="form-table js-wpkpidb-years-table" id="js-wpkpidb-years-table-<?php echo esc_attr( $year ); ?>">
        <tbody>
        <?php for( $month = 1; $month <= 12; $month++ ):
          if( isset( $pageview_datas ) && isset( $pageview_datas[$year] ) ) {
            $value = $pageview_datas[$year][$month];
          } else {
            $value = '';
          }
          ?>
          <tr>
            <th>
              <label for="<?php echo esc_attr( $month ); ?>">
                <?php $helper->e( $months_name[$month - 1] ); ?>
              </label>
            </th>
            <td>
              <input type="number" name="pv_kpi[<?php echo esc_attr( $year ); ?>][<?php echo esc_attr( $month ); ?>]" value="<?php echo esc_html( $value ); ?>">
            </td>
          </tr>
        <?php endfor; ?>
        </tbody>
      </table>
    <?php endfor; ?>
    <p class="submit">
      <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php $helper->e( 'Save' ); ?>">
    </p>
  </form>
</div>
