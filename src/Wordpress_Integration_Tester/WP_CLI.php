<?php

/**
* Run WIT_Tests with the WPCLI
*/

class WIT_CLI {

  /**
  *
  * Run tests using the WordPress Integration Tester module
  *
  * @param void
  * @return void
  * @hooked WP_CLI: wp wit run
  *
  **/

  function run() {

    global $progress;

    $controller = new Wordpress_Integration_Tester\WIT_Test_Controller;
    $progress = WP_CLI\Utils\make_progress_bar( 'Executing tests...' , $controller->get_test_methods_count());
    $controller->execute_tests();
    $results = $controller->__get_results();

    $results_output = array();

    foreach( $results as $result ) {
      $results_output[] = array(
        'Class' => $result['class'],
        'Test' => $result['description'],
        'Success' => ( $result['success'] ) ? WP_CLI::colorize( "%Gpass%n " ) : WP_CLI::colorize( "%Rfail%n " ),
      );
    }

    WP_CLI\Utils\format_items( 'table', $results_output , array(
      'Class',
      'Test',
      'Success',
    ));

  }
  
}

if( class_exists( 'WP_CLI' )) {
  WP_CLI::add_command( 'wit' , 'WIT_CLI' );
}
