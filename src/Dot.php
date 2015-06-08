<?php

namespace Mduk;

class Dot {
  protected $array = [];

  public function __construct( array $array = [] ) {
    $this->array = $array;
  }

  public function get( $dot ) {
    $dots = explode( '.', $dot );
    return $this->getting( $this->array, $dots );
  }

  public function set( $dot, $value ) {
    $dots = explode( '.', $dot );
    return $this->setting( $this->array, $dots, $value );
  }

  public function getArray() {
    return $this->array;
  }

  protected function getting( $array, $dots ) {
    if ( count( $dots ) == 1 ) {
      return $array[ $dots[0] ];
    }

    $dot = array_shift( $dots );
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

