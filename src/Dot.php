<?php

namespace Mduk;

class Dot {
  protected $array = [];

  public function __construct( array $array = [] ) {
    $this->array = $array;
  }

  public function get( $dottyKey ) {
    return $this->getting(
      $this->array,
      $this->expandDottyKey( $dottyKey )
    );
  }

  public function set( $dottyKey, $value ) {
    $this->setting(
      $this->array,
      $this->expandDottyKey( $dottyKey ),
      $value
    );
  }

  public function getArray() {
    return $this->array;
  }

  protected function expandDottyKey( $dottyKey ) {
    if ( $dottyKey == '' ) {
      throw new Dot\Exception\InvalidKey(
        "Key cannot be blank"
      );
    }

    return explode( '.', $dottyKey );
  }

  public function flatten() {
    $accumulator = [];
    $this->flattening( '', $this->array, $accumulator );
    return $accumulator;
  }

  protected function flattening( $keyPrefix, $array, &$accumulator ) {
    foreach ( $array as $key => $value ) {
      if ( $keyPrefix ) {
        $flatKey = "{$keyPrefix}.{$key}";
      }
      else {
        $flatKey = $key;
      }

      if ( is_array( $value ) ) {
        $this->flattening( $flatKey, $value, $accumulator );
      }
      else {
        $accumulator[ $flatKey ] = $value;
      }
    }
  }

  protected function getting( $currentNode, $remainingKeys ) {
    // If there are no keys left then we have reached our destination,
    //   just return the current node
    if ( count( $remainingKeys ) == 0 ) {
      return $currentNode;
    }

    // We have more keys to follow. Check that the current node can be traversed.
    $nodeIsNotAnArray = !is_array( $currentNode );
    $nodeIsNotAnArrayObject = !( $currentNode instanceof \ArrayObject );
    $nodeIsNotAnArrayAccess = !( $currentNode instanceof \ArrayAccess );
    if ( $nodeIsNotAnArray && $nodeIsNotAnArrayObject && $nodeIsNotAnArrayAccess ) {
      throw new Dot\Exception\DotOverflow(
        "Cannot go any deeper. The current node is neither an array nor an \\ArrayObject."
      );
    }

    // Grab the next key
    $nextKey = array_shift( $remainingKeys );

    // Check that the key we want to retrieve exists on the current node
    //   We have to check with both array_key_exists() and isset() because:
    //
    //   1) isset() considers unset and set-and-null to be equivalent.
    //   2) array_key_exists() works on arrays and ArrayObject, but not on ArrayAccess.
    if ( !array_key_exists( $nextKey, $currentNode ) && !isset( $currentNode[ $nextKey ] ) ) {
      throw new Dot\Exception\InvalidKey(
        "Key doesn't exist: {$nextKey}"
      );
    }

    // Get the next node
    $nextNode = $currentNode[ $nextKey ];

    // Recurse
    return $this->getting( $nextNode, $remainingKeys );
  }

  protected function setting( &$array, $dots, $value ) {
    if ( count( $dots ) == 1 ) {
      return $array[ $dots[0] ] = $value;
    }

    $dot = array_shift( $dots );

    if ( !isset( $array[ $dot ] ) || !is_array( $array[ $dot ] ) ) {
      $array[ $dot ] = [];
    }

    $this->setting( $array[ $dot ], $dots, $value );
  }
}

