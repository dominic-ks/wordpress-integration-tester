<?php

namespace Wordpress_Integration_Tester;

/**
*
* The Test Class for running tests
*
**/

class WIT_Test {
  
  
  /**
  *
  * The test results
  *
  **/
  
  private $results;
  
  
  /**
  *
  * The index of the current method being tested
  *
  **/
  
  private $current_method_index;
  
  
  /**
  *
  * The status of this test class
  *
  **/
  
  private $test_status;
  
  
  /**
  *
  * The class constructor
  *
  * @param $method_index int the method index to start from
  * @return void
  *
  **/
  
  public function __construct( $method_index = 0 ) {
    $this->current_method_index = $method_index;
    $this->results = array();
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
  * Run set up before each test
  *
  * @param void
  * @return void
  *
  **/
  
  protected function __setup() {
    
  }
  
  
  /**
  *
  * Close down and reset post-test
  *
  * @param void
  * @return void
  *
  **/
  
  protected function __close_down() {
    
  }
  
  
  /**
  *
  * Get the status of the tests in this class
  *
  * @param void
  * @return str the current status, either not-started, testing or complete
  *
  **/
  
  public function __get_status() {
    
    if( $this->current_method_index === 0 ) {
      return 'not-starter';
    }
    
    return ( $this->current_method_index >= count( $this->__get_class_methods())) ? 'complete' : 'testing';
    
  }
  
  
  /**
  *
  * Load a DB dump
  *
  * @param $path the path to the DB dump
  * @return the return from the shell
  *
  **/
  
  protected function __load_db( $path ) {
    $command = 'wp db import ' . $path;
    return shell_exec( $command );
  }
  
  
  /**
  *
  * Check if two values are equal and return a test result
  *
  * @args arr an array of args for this function
  *  - $expected mixed the expected value
  *  - $actual mixed the actual value
  *  - $description str the test description
  *  - $data arr additional data to pass to the test result
  *  - $exact bool whether to check for an exact match, uses === if true, == if false
  * @return arr a test result array
  *
  **/
  
  protected function __are_equal( $args = array() ) {
    
    extract( wp_parse_args( $args , array(
      'expected' => 0,
      'actual' => 1,
      'description' => 'No description was provided.',
      'data' => array(),
      'exact' => true,
    )));
    
    $success = ( $exact ) ? $expected === $actual : $expected == $actual;
    
    $this->results[] = array(
      'success' => $success,
      'description' => $description,
      'class' => get_class( $this ),
      'data' => array_merge( array(
        'expected' => $expected,
        'actual' => $actual,
      ), $data ),
    );
    
  }
  
  
  /**
  *
  * Check if two values are NOT equal and return a test result
  *
  * @args arr an array of args for this function
  *  - $expected mixed the expected value
  *  - $actual mixed the actual value
  *  - $description str the test description
  *  - $data arr additional data to pass to the test result
  *  - $exact bool whether to check for an exact match, uses === if true, == if false
  * @return arr a test result array
  *
  **/
  
  protected function __are_not_equal( $args = array() ) {
    
    extract( wp_parse_args( $args , array(
      'expected' => 0,
      'actual' => 0,
      'description' => 'No description was provided.',
      'data' => array(),
      'exact' => true,
    )));
    
    $success = ( $exact ) ? $expected !== $actual : $expected != $actual;
    
    $this->results[] = array(
      'success' => $success,
      'description' => $description,
      'class' => get_class( $this ),
      'data' => array_merge( array(
        'expected' => $expected,
        'actual' => $actual,
      ), $data ),
    );
    
  }
  
  
  /**
  *
  * Get the class methods to test
  *
  * @param void
  * @return arr an array of methods to test
  *
  **/
  
  public function __get_class_methods() {
    
    $methods = get_class_methods( $this );
    $return_methods = array();
    
    foreach( $methods as $method ) {
      
      if( strpos( $method , '__' ) === 0 ) {
        continue;
      }
      
      $return_methods[] = $method;
      
    }
    
    return $return_methods;
    
  }
  
  
  /**
  *
  * Execute tests
  *
  * @param $limit int the number of tests to run
  * @return arr the current set of results for this class
  *
  **/
  
  public function __execute_tests( $limit = 10 ) {
    
    $methods = $this->__get_class_methods();
    $start_index = $this->current_method_index;
    $limit = ( $start_index + $limit > count( $methods )) ? count( $methods ) : $start_index + $limit;
      
    $this->__setup();
    
    for( $i = $start_index; $i < $limit; $i++ ) {      
      $method = $methods[ $i ];      
      $this->$method();      
      $this->current_method_index = $this->current_method_index + 1;      
    }
    
    $this->__close_down();
    
    return $this;
    
  }
  
  
}
