<?php

namespace Wordpress_Integration_Tester;

/**
*
* The Test Controller where all actions should be initiated
*
**/

class WIT_Test_Controller {
  
  
  /**
  *
  * The test classes to run
  *
  **/
  
  private $classes;
  
  
  /**
  *
  * The index of the current test class
  *
  **/
  
  private $current_class_index;
  
  
  /**
  *
  * The index of the current method being tested
  *
  **/
  
  private $current_method_index;
  
  
  /**
  *
  * The number of tests to run in one instance
  *
  **/
  
  private $test_interval;
  
  
  /**
  *
  * The test results
  *
  **/
  
  private $results;
  
  
  /**
  *
  * The current status of the test run
  *
  **/
  
  private $status;
  
  
  /**
  *
  * The class constructor
  *
  * @param $class_index int the class index to start from
  * @param $method_index int the method index to start from
  * @return void
  *
  **/
  
  public function __construct( $class_index = 0 , $method_index = 0  ) {
    $this->classes = $this->get_test_classes();
    $this->current_class_index = $class_index;
    $this->current_method_index = $method_index;
    $this->status = 'started';
    $this->test_interval = apply_filters( 'wit_test_interval' , 1 );
  }
  
  
  /**
  *
  * Get the results
  *
  * @param void
  * @return arr the results
  *
  **/
  
  public function __get_results() {
    return $this->results;
  }
  
  
  /**
  *
  * Get the current classes index
  * 
  * @param void
  * @return int the current methods index
  *
  **/
  
  public function __get_class_index() {
    return $this->current_class_index;
  }
  
  
  /**
  *
  * Get the current methods index
  * 
  * @param void
  * @return int the current methods index
  *
  **/
  
  public function __get_method_index() {
    return $this->current_method_index;
  }
  
  
  /**
  *
  * Get the current status of the tests
  *
  * @param void
  * @return str the current status
  *
  **/
  
  public function __get_status() {
    return ( $this->current_class_index + 1 > count( $this->classes )) ? 'complete' : 'testing';
  }
  
  
  /**
  *
  * Get all test classes
  *
  * @param void
  * @return arr an array of test classes
  *
  **/
  
  private function get_test_classes() {
    
    $classes = array();
    
    foreach( get_declared_classes() as $class ) {
      if( is_subclass_of( $class , 'Wordpress_Integration_Tester\WIT_Test' )) {
        $classes[] = $class;
      }
    }
    
    return $classes;
    
  }
  
  
  /**
  *
  * Execute tests
  *
  * @param void
  * @return void
  *
  **/
  
  public function execute_tests() {
    
    $results = array();
    $start_index = $this->current_class_index;
    
    for( $i = $start_index; $i < count( $this->classes ); $i++ ) {
      
      $class = $this->classes[ $i ];
      $object = new $class( $this->current_method_index );
      
      $object->__execute_tests( $this->test_interval );
      
      $results = array_merge( $results , $object->__get_results());
      
      $this->current_class_index = ( $object->__get_status() !== 'complete' ) ? $this->current_class_index : $this->current_class_index + 1;
      $this->current_method_index = ( $object->__get_status() !== 'complete' ) ? $object->__get_method_index() : 0;
      break;
      
    }
    
    $this->status = $this->__get_status();
    
    $this->results = $results;
    return $this;
    
  }
  

}