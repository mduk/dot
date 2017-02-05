<?php

namespace Mduk;

class ArrayAccessible implements \ArrayAccess {
  public function offsetExists( $o ) {
    return true;
  }

  public function offsetGet( $o ) {
    return "yay {$o}!";
  }

  public function offsetSet( $o, $v ) {

  }

  public function offsetUnset( $o ) {

  }
}

class DotTest extends \PHPUnit_Framework_TestCase {
  protected $dot;

  public function setUp() {
    $this->dot = new Dot( [
      'foo' => [
        'bar' => 'baz'
      ],
      'null' => null,
      'array_access' => new ArrayAccessible
    ] );
  }

  public function testFlatten() {
    $dot = new Dot( [
      'foo' => [
        'bar' => [
          'baz' => 'baz'
        ],
        'qha' => [
          'waz' => 'nih'
        ]
      ]
    ] );
    $this->assertEquals( [ 'foo.bar.baz' => 'baz', 'foo.qha.waz' => 'nih' ], $dot->flatten(),
      "Flatten should work" );
  }

  public function testGet() {
    $this->assertEquals( 'baz', $this->dot->get( 'foo.bar' ),
      "Getting foo.bar should have returned baz" );
  }

  public function testGetNull() {
    $this->assertNull( $this->dot->get( 'null' ),
      "Getting null should have returned null" );
  }

  public function testGetArrayAccess() {
    $this->assertEquals( 'yay foo!', $this->dot->get( 'array_access.foo' ),
      "Getting array_access.foo should have returned 'yay foo!'" );
  }

  public function testSet() {
    $return = $this->dot->set( 'foo.baz', 'bacon' );

    $this->assertNull( $return,
      "set() should have returned null" );

    $this->assertEquals( 'bacon', $this->dot->get( 'foo.baz' ),
      "Getting foo.baz should have returned bacon" );
  }

  public function testOverwrite() {
    $this->dot->set( 'over.write', 'me' );
    $this->dot->set( 'over.write', 'you' );

    $this->assertEquals( 'you', $this->dot->get( 'over.write' ),
      "Getting over.write should have returned you" );
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

  public function testSetError_BlankKey() {
    $dot = new Dot([]);

    try {
      $dot->set( '', 42 );
      $this->fail();
    }
    catch ( Dot\Exception\InvalidKey $e ) {}
  }

  public function testGetError_BlankKey() {
    $dot = new Dot([]);

    try {
      $dot->get( '' );
      $this->fail();
    }
    catch ( Dot\Exception\InvalidKey $e ) {}
  }

  public function testGetError_KeyDoesntExist() {
    $dot = new Dot([
      'a' => [
        'b' => 'bValue'
      ]
    ]);

    try {
      $dot->get( 'a.b.c' );
      $this->fail('A DotOverflow exception should have been thrown');
    }
    catch ( Dot\Exception\InvalidKey $e ) {}
  }

  public function testGetError_KeyNotDefined() {
    $dot = new Dot([
      'a' => [
        'b' => 'bValue'
      ]
    ]);

    try {
      $dot->get('a.c');
      $this->fail('An InvalidKey exception should have been thrown');
    }
    catch ( Dot\Exception\DotOverflow $e ) {}
  }
}

