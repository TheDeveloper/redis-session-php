<?php

class GoSquaredAdminSDKTest extends PHPUnit_Framework_TestCase{

  //public function setUp(){
  //}

  public function testGetData(){
    $d = $_SESSION['test'];
    $this->assertEquals("ohai", $d);
  }

  public function testDestroy(){
    session_destroy();
  }
}
?>
