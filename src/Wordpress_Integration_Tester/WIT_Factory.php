<?php

namespace Wordpress_Integration_Tester;

/**
*
* The Factory Class for general setup and bootstrapping
*
**/

class WIT_Factory {
  
  
  /**
  *
  * The class constructor
  *
  * @param void
  * @return void
  *
  **/
  
  public function __construct() {
    include_once( 'WIT_API.php' );
  }
  
  
}