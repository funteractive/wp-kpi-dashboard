<?php

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function example_add_dashboard_widgets() {

  wp_add_dashboard_widget(
    'example_dashboard_widget',         // Widget slug.
    'Example Dashboard Widget',         // Title.
    'example_dashboard_widget_function' // Display function.
  );
}
add_action( 'wp_dashboard_setup', 'example_add_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function example_dashboard_widget_function() {

  // Display whatever it is you want to show.
  echo "Hello World, I'm a great Dashboard Widget";
}
