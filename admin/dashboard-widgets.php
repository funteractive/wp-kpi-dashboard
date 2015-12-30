<?php

// Don't allow plugin to be loaded directory
if ( !defined( 'ABSPATH' ) )
  exit();


/**
 * Class WpKpiDashboard_Widgets
 */
class WpKpiDashboard_Widgets
{

  protected $period_name = 'wpkpidb_period';

  /**
   * WpKpiDashboard_Widgets constructor.
   */
  public function __construct() {
    // include helper
    require_once( WP_KPI_DASHBOARD_DIR . 'app/helper.php' );
    $this->helper = new WpKpiDashboard_Helper();

    // include services
    require_once( WP_KPI_DASHBOARD_DIR . 'app/auth/google.php' );
    $this->google = new WpKpiDashboard_Google();

    require_once( WP_KPI_DASHBOARD_DIR . 'app/services/pageview.php' );
    $this->pageview = new WpKpiDashboard_Pageview();

    // Set hooks
    add_action( 'wp_dashboard_setup', array( &$this, 'add_dashboard_widgets' ) );
  }

  /**
   * Add dashboard widgets.
   */
  public function add_dashboard_widgets() {
    wp_add_dashboard_widget(
      'wp_kpi_dashboard',                     // Widget slug.
      $this->helper->_( 'WP KPI Dashboard' ), // Title.
      [ &$this, 'init_widget' ]               // Display function.
    );
  }

  /**
   * Create the function to output the contents of our Dashboard Widget.
   */
  public function init_widget() {
    $period = $this->get_period();

    $html = $this->get_select_period_html( $period );

    $kpi = $this->get_pageview_kpi( $period );
    $pageview_data = $this->get_pageview_data( $period );
    $html .= $this->get_kpi_block_html( $kpi, $pageview_data, 'Pageview' );

    echo $html;
  }

  /**
   * @param $kpi
   * @param $pageview_data
   * @param $title
   * @return mixed|string|void
   */
  private function get_kpi_block_html( $kpi, $pageview_data, $title ) {
    $html = <<<EOL
<div class="wpkpi_db_block">
  <p class="wpkpi_db_block_title">{$title}</p>
  <span class="wpkpi_db_value">{$pageview_data}</span>
  <span class="wpkpi_db_divider">/</span>
  <span class="wpkpi_db_kpi">{$kpi}</span>
</div>
EOL;

    $html = apply_filters( 'wpkpidb_dashboard_block_html', $html );
    return $html;
  }

  /**
   * @param $period
   * @return mixed|string|void
   */
  private function get_select_period_html( $period ) {
    $period_arr = [ 'Daily', 'Monthly', 'Yearly' ];
    $options = '';
    foreach( $period_arr as $value ) {
      if( $period === $value ) {
        $selected = ' selected';
      } else {
        $selected = '';
      }
      $options .= sprintf( '<option value="%s"%s>%s</option>', $value, $selected, $value );
    }
    $html = <<<EOL
<form id="js-wpkpidb-db-form" action ="" method="POST">
  <select id="js-wpkpidb-db-period-select" name="{$this->period_name}">
    {$options}
  </select>
</form>
EOL;

    $html = apply_filters( 'wpkpidb_dashboard_period_select_html', $html );
    return $html;
  }

  /**
   * @param $period
   * @return float|int
   */
  private function get_pageview_kpi( $period ) {
    $page_view_kpi = $this->pageview->get_kpi();
    $year = date( 'Y' );
    $month = date( 'n' );

    switch( $period ) {
      case 'Daily':
        $monthly_kpi = $page_view_kpi[$year][$month];
        $dates = date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
        $show_kpi = round( $monthly_kpi / $dates, 1 );
        break;
      case 'Monthly':
        $show_kpi = $page_view_kpi[$year][$month];
        break;
      case 'Yearly':
        $show_kpi = 0;
        $yearly_kpi_arr = $page_view_kpi[$year];

        foreach( $yearly_kpi_arr as $monthly_kpi ) {
          $show_kpi += $monthly_kpi;
        }
        break;
    }

    return $show_kpi;
  }

  /**
   * @return string
   */
  private function get_period() {
    if( isset( $_POST[$this->period_name] ) && $_POST[$this->period_name] ) {
      $period = esc_html( $_POST[$this->period_name] );
    } else {
      $period = 'Monthly';
    }

    return $period;
  }

  /**
   * @param $period
   * @return bool
   */
  private function get_pageview_data( $period ) {
    $year = date( 'Y' );
    $month = date( 'm' );
    $date = date( 'd' );
    $days = date( 't' );

    switch( $period ) {
      case 'Daily':
        $start_date = 'today';
        $end_date = 'today';
        break;
      case 'Monthly':
        $start_date = $year . '-' . $month . '-01';
        $end_date = $year . '-' . $month . '-' . $days;
        break;
      case 'Yearly':
        $start_date = $year . '-01-01';
        $end_date = $year . '-12-31';
        break;
    }

    $data_pageview = $this->google->dashboard_get_gadata( $start_date, $end_date );

    return $data_pageview;
  }

}
