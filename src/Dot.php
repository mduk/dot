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
    return $this->setting(
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

  protected function getting( $array, $dots ) {
    if ( count( $dots ) == 1 ) {
      return $this->dotValue( $dots[0], $array );
    }

    $dot = array_shift( $dots );
    $dotValue = $this->dotValue( $dot, $array );
    return $this->getting( $dotValue, $dots );
  }

  protected function setting( &$array, $dots, $value ) {
    if ( count( $dots ) == 1 ) {
      return $array[ $dots[0] ] = $value;
    }

    $dot = array_shift( $dots );

    if ( !isset( $array[ $dot ] ) || !is_array( $array[ $dot ] ) ) {
      $array[ $dot ] = [];
    }

    return $this->setting( $array[ $dot ], $dots, $value );
  }

  protected function dotValue( $dot, $array ) {
    if ( !is_array( $array ) && !( $array instanceof \ArrayAccess ) ) {
      throw new Dot\Exception\DotOverflow(
        "{$dot} is not an array or \\ArrayAccess object."
      );
    }

    if ( !array_key_exists( $dot, $array ) && !isset( $array[ $dot ] ) ) {
      throw new Dot\Exception\InvalidKey(
        "Invalid key: {$dot}"
      );
    }

    return $array[ $dot ];
  }
}

