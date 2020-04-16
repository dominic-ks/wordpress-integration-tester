<?php

use Wordpress_Integration_Tester\WIT_Test_Controller;

/**
*
* Access WIT via the WP REST API
*
**/

add_action( 'rest_api_init', function () {  
  register_rest_route( 'wit/v1' , '/run-tests' , array(
    'methods' => 'POST',
    'callback' => function( $data ) {
      
      $params = $data->get_params();
      $class_index = ( isset( $params['class_index'] )) ? $params['class_index'] : 0;
      $method_index = ( isset( $params['method_index'] )) ? $params['method_index'] : 0;
      
      try {
        $controller = new WIT_Test_Controller( $class_index , $method_index );
        $controller->execute_tests();
        return array(
          'results' => $controller->__get_results(),
          'class_index' => $controller->__get_class_index(),
          'method_index' => $controller->__get_method_index(),
          'status' => $controller->__get_status(),
        );
      }
      
      catch( Exception $e ) {
        return new WP_Error( 'error' , $e->getMessage() , array( 'status' => 500 ));
      }
      
    },
    'permission_callback' => function() {
      return true; 
    },
  ));  
});