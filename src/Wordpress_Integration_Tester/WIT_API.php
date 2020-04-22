<?php

use Wordpress_Integration_Tester\WIT_Test_Controller;


/**
*
* Run WIT Tests via the WP REST API
*
**/

add_action( 'rest_api_init', function () {  
  register_rest_route( 'wit/v1' , '/run-tests' , array(
    'methods' => 'POST',
    'callback' => function( $data ) {
      
      $params = $data->get_params();
      $class_index = ( isset( $params['class_index'] )) ? $params['class_index'] : 0;
      $method_index = ( isset( $params['method_index'] )) ? $params['method_index'] : 0;
      $test_interval = ( isset( $params['test_interval'] )) ? $params['test_interval'] : 10;
      
      try {
        $controller = new WIT_Test_Controller( $class_index , $method_index , $test_interval );
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


/**
*
* Get the available classes for testing
*
**/

add_action( 'rest_api_init', function () {  
  register_rest_route( 'wit/v1' , '/get-classes' , array(
    'methods' => 'GET',
    'callback' => function( $data ) {      
      $controller = new WIT_Test_Controller();
      return $controller->get_test_classes();      
    },
    'permission_callback' => function() {
      return true; 
    },
  ));  
});


/**
*
* Get the available test methods for a class
*
**/

add_action( 'rest_api_init', function () {  
  register_rest_route( 'wit/v1' , '/get-class-methods' , array(
    'methods' => 'GET',
    'callback' => function( $data ) {      
      
      $params = $data->get_params();
      
      if( ! isset( $params['class_name'] )) {
        return new WP_Error( 'error' , 'You must supply a class name.' , array( 'status' => 500 ));
      }
      
      $class = $params['class_name'];
      
      if( ! is_subclass_of( $class , 'Wordpress_Integration_Tester\WIT_Test' )) {
        return new WP_Error( 'error' , 'The supplied class does not extend WIT_Test.' , array( 'status' => 500 ));
      }
      
      $object = new $class;
      return $object->__get_class_methods();
      
    },
    'permission_callback' => function() {
      return true; 
    },
  ));  
});
