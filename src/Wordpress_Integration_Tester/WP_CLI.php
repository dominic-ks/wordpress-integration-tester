<?php

/**
* Run WIT_Tests with the WPCLI
*/

class WIT_CLI {

  /**
  *
  * Run tests using the WordPress Integration Tester module
  *
  * --class=<clacc>
  * : The name of a class if we're just going to run a single test class
  *
  * @hooked WP_CLI: wp wit run
  *
  **/

  function run( $args = array() , $assoc_args = array()) {

    global $progress;

    $controller = new Wordpress_Integration_Tester\WIT_Test_Controller;
    $progress = WP_CLI\Utils\make_progress_bar( 'Executing tests...' , $controller->get_test_methods_count());
    
    $test_args = array();
    
    if( $assoc_args['class'] ) {
      $test_args['_class_to_test'] = $assoc_args['class'];
    }
    
    $controller->execute_tests( $test_args );
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
