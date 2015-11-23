<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

// Include Admin Class
require_once( WP_KPI_DASHBOARD_DIR . 'app/admin.php' );
$admin = new WpKpiDashboard_Admin();

// Include Helper Class
require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
$helper = new WpKpiDashboard_Helper();

// setup
$admin->setup();

// months name
$months_name = [
  'January',
  'February',
  'March',
  'April',
  'May',
  'June',
  'July',
  'August',
  'September',
  'October',
  'November',
  'December'
];

// create nonce
$nonce = wp_create_nonce( $admin->save_action );

// default year
$start_year = 2015;
$end_year = 2030;
if( isset( $_GET['year'] ) && $_GET['year'] && ( $start_year <= $_GET['year'] && $_GET['year'] <= $end_year ) ) {
  $default_year = esc_html( $_GET['year'] );
} else {
  $default_year = date( 'Y' );
}
?>

<div class="wrap">
  <h2><?php $helper->e( 'WP KPI Dashboard' ); ?></h2>

  <form action="<?php echo admin_url( 'options-general.php?page=' . WP_KPI_DASHBOARD_DOMAIN ); ?>" method="POST">
    <select name="year">
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

    <table class="form-table">
      <tbody>
      <?php for( $month = 1; $month <= 12; $month++ ): ?>
        <tr>
          <th>
            <label for="<?php echo esc_attr( $month ); ?>">
              <?php $helper->e( $months_name[$month - 1] ); ?>
            </label>
          </th>
          <td>
            <input type="number" name="month_<?php echo esc_attr( $month ); ?>">
          </td>
        </tr>
      <?php endfor; ?>
      </tbody>
    </table>
    <p class="submit">
      <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php $helper->e( 'Save' ); ?>">
    </p>
  </form>
</div>
