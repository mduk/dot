<?php

namespace Mduk;

/**
 * Dot
 *
 * Dot helps simplify working with deep array and array-like structures.
 *
 * Terminology:
 *
 *    Dot Tree: An instance of Mduk\Dot that wraps an array or array-like structure.
 *
 *  Array-like: An object that can behave like an native array would. This includes
 *              instances of classes that implements both \ArrayAccess and \Iterator,
 *              or any object who's class extends \ArrayObject.
 *
 *         Key: A string that can be used to address a node within a Dot Tree.
 *              Eg: 'foo.bar.baz'
 *
 *        Dots: An array of individual array keys that are used internally to navigate
 *              the Dot Tree. Eg: ['foo', 'bar', 'baz']
 *
 * @author Daniel Kendell <daniel@starling-systems.co.uk>
 */
class Dot {

  /**
   * @var array
   */
  protected $array = [];

  public function __construct( array $array = [] ) {
    $this->array = $array;
  }

  /**
   * Get
   *
   * Extract a sub-tree or node value from the deep Array
   *
   * @param string $dottyKey The address of a node to retrieve
   *
   * @return mixed The node value or sub-tree.
   */
  public function get( $dottyKey ) {
    return $this->getting(
      $this->array,
      $this->expandDottyKey( $dottyKey )
    );
  }

  /**
   * Set
   *
   * Update a node with a new value or sub-tree.
   *
   * @param string $dottyKey The address of a node to set
   */
  public function set( $dottyKey, $value ) {
    $this->setting(
      $this->array,
      $this->expandDottyKey( $dottyKey ),
      $value
    );
  }

  /**
   * Get Array
   *
   * @return array The original Array structure.
   */
  public function getArray() {
    return $this->array;
  }

  /**
   * Expand Dotty Key
   *
   * @param string $dottyKey The key to convert into an array of dots for navigating down the tree with
   *
   * @return array Array of dots
   */
  protected function expandDottyKey( $dottyKey ) {
    if ( $dottyKey == '' ) {
      throw new Dot\Exception\InvalidKey(
        "Key cannot be blank"
      );
    }

    return explode( '.', $dottyKey );
  }

  /**
   * Flatten
   *
   * @return array A flattened representation of the deep array with dotted keys
   */
  public function flatten() {
    $accumulator = [];
    $this->flattening( '', $this->array, $accumulator );
    return $accumulator;
  }

  /**
   * Flattening
   *
   * Recursive counterpart to the flatten() public method.
   *
   * @param string $keyPrefix After the sub-tree to flatten has been selected, the dots that made up the key until that point will have been lost so we pass it in here to prefix the flattened keys with
   * @param array $array The sub-tree of the Dot Tree to flatten down
   * @param array &$accumulator This is the associative array that will eventually be returned to the caller
   */
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

  /**
   * Getting
   *
   * Recursive counterpart to the get() public method.
   *
   * @param array $currentNode The current tree node to either navigate through or return
   * @param array $remainingKeys An array of keys left to traverse
   *
   * @return mixed The value of node that was navigated to
   */
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
      throw new Dot\Exception\KeyOverflow(
        'Cannot go any deeper, the current node is not traversable. '
          . 'Node must be an array, \\ArrayObject or \\ArrayAccess instance.'
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

  /**
   * Setting
   *
   * Recursive counterpart to the set() public method.
   *
   * @param array $array The ArrayTree to navigate
   * @param array $dots  A series of Dots that make up the DottyKey for the node to set
   * @param mixed $value The value to set at the location specified by the Dots
   */
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

