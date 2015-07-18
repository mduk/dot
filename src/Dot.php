<?php

namespace Mduk;

class Dot {
  protected $array = [];

  public function __construct( array $array = [] ) {
    $this->array = $array;
  }

  public function get( $key ) {
    return $this->getting(
      $this->array,
      $this->keyToDots( $key )
    );
  }

  public function set( $key, $value ) {
    return $this->setting(
      $this->array,
      $this->keyToDots( $key ),
      $value
    );
  }

  public function getArray() {
    return $this->array;
  }

  public function keyToDots( $key ) {
    if ( $key == '' ) {
      throw new Dot\Exception\InvalidKey(
        "Key cannot be blank"
      );
    }

    return explode( '.', $key );
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
      if ( !array_key_exists( $dots[0], $array ) && !isset( $array[ $dots[0] ] ) ) {
        throw new Dot\Exception\InvalidKey(
          "Invalid key: {$dots[0]}"
        );
      }
      return $array[ $dots[0] ];
    }

    $dot = array_shift( $dots );

    if ( !array_key_exists( $dot, $array ) && !isset( $array[ $dot ] ) ) {
      throw new Dot\Exception\InvalidKey(
        "Invalid key: {$dot}"
      );
    }

    return $this->getting( $array[ $dot ], $dots );
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
}

