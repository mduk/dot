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

  public function testSetArray() {
    $array = [
      'one' => [
        'two' => 'three'
      ]
    ];
    $this->dot->set( 'set.array', $array );

    $this->assertEquals( $array, $this->dot->get( 'set.array' ),
      "Getting set.array should have returned the original array" );

    $this->assertEquals( 'three', $this->dot->get( 'set.array.one.two' ),
      "Getting set.array.one.two should have returned 'three'" );
  }
}

