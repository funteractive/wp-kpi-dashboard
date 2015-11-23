<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

// Include Admin Class
require_once( WP_KPI_DASHBOARD_DIR . 'admin/admin.php' );
$admin = new WpKpiDashboard_Admin();

// Include Helper Class
require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
$helper = new WpKpiDashboard_Helper();

// setup
$datas = $admin->template_setup();

// months name
$months_name = $admin->months_name;

// create nonce
$nonce = wp_create_nonce( $admin->save_action );

// default year
$start_year = $admin->start_year;
$end_year = $admin->end_year;
$default_year = date( 'Y' );
?>

<div class="wrap">
  <h2><?php $helper->e( 'WP KPI Dashboard' ); ?></h2>

  <select name="year" id="js-wpkpid-years-select">
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
      <table class="form-table js-wpkpid-years-table" id="js-wpkpid-years-table-<?php echo esc_attr( $year ); ?>">
        <tbody>
        <?php for( $month = 1; $month <= 12; $month++ ):
          if( isset( $datas ) && isset( $datas[$year] ) ) {
            $value = $datas[$year][$month];
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
