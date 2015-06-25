<?php

namespace Mduk;

class DotTest extends \PHPUnit_Framework_TestCase {
  protected $dot;

  public function setUp() {
    $this->dot = new Dot( [
      'foo' => [
        'bar' => 'baz'
      ],
      'null' => null
    ] );
  }

  public function testGet() {
    $this->assertEquals( 'baz', $this->dot->get( 'foo.bar' ),
      "Getting foo.bar should have returned baz" );
  }

  public function testGetNull() {
    $this->assertNull( $this->dot->get( 'null' ),
      "Getting null should have returned null" );
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

  public function testSetInvalidKey_BlankKey() {
    try {
      $this->dot->set( '', 42 );
      $this->fail();
    }
    catch ( Dot\Exception\InvalidKey $e ) {}
  }

  public function testGetInvalidKey_KeyDoesntExist() {
    try {
      $this->dot->get( 'a.b.d.e' );
      $this->fail();
    }
    catch ( Dot\Exception\InvalidKey $e ) {}
  }

  public function testGetInvalidKey_BlankKey() {
    try {
      $this->dot->get( '' );
      $this->fail();
    }
    catch ( Dot\Exception\InvalidKey $e ) {}
  }
}

