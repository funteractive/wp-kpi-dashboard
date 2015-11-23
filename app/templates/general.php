<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();

// Include Admin Class
require_once( WP_KPI_DASHBOARD_DIR . 'app/admin.php' );
require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
$helper = new WpKpiDashboard_Helper();

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
]
?>

<div class="wrap">
  <h2><?php $helper->e( 'WP KPI Dashboard' ); ?></h2>

  <select name="" id="">
    <?php for( $year = 2015; $year <= 2030; $year++ ): ?>
      <option value="<?php echo esc_attr( $year ); ?>"><?php echo esc_html( $year ); ?></option>
    <?php endfor; ?>
  </select>

  <form action="<?php echo admin_url( 'options-general.php?page=' . WP_KPI_DASHBOARD_DOMAIN ); ?>" method="POST">
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
      <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php $helper->e( 'Save' ); ?>">
    </p>
  </form>
</div>
