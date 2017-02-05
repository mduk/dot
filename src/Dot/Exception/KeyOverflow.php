<?php

namespace Mduk\Dot\Exception;

use Mduk\Dot\Exception;

/**
 * Key Overflow
 *
 * This exception is thrown when, during the course of traversing the tree, a
 * leaf node is prematurely found.
 */
class KeyOverflow extends Exception {}
