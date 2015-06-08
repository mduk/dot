<?php

namespace Mduk;

class DotTest extends \PHPUnit_Framework_TestCase {
  protected $dot;

  public function setUp() {
    $this->dot = new Dot( [
      'foo' => [
        'bar' => 'baz'
      ]
    ] );
  }

  public function testGet() {
    $this->assertEquals( 'baz', $this->dot->get( 'foo.bar' ),
      "Getting foo.bar should have returned baz" );
  }

  public function testSet() {
    $this->dot->set( 'foo.baz', 'bacon' );
    $this->assertEquals( 'bacon', $this->dot->get( 'foo.baz' ),
      "Getting foo.bar should have returned baz" );
  }

  public function testOverwrite() {
    $this->dot->set( 'over.write', 'me' );
    $this->dot->set( 'over.write', 'you' );

    $this->assertEquals( 'you', $this->dot->get( 'over.write' ),
      "Getting foo.bar should have returned baz" );
  }
}

