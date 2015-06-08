# Dot

Just a little something to make working with config arrays easier.

## Example

```
$config = [
  'a' => [
    'b' => [
      'c' => [
        'd' => 'e!'
      ]
    ]
  ]
];

$dot = new Mduk\Dot( $config );

// Fetch a particular key
$dot->get( 'a.b.c.d' ); // 'e!'

// Set a key with dot notation
$dot->set( 'a.b.c.e', 'f!' );

// Fetch a key further up to get lower keys as a deep array
$dot->get( 'a.b.c' ); // [ 'd' => 'e!', 'e' => 'f!' ]

// Creating child keys of a key will overwrite any previously set value
$dot->set( 'a.b.c.d.e', 'g?' );

$dot->get( 'a.b.c' ); // [ 'd' => [ 'e' => 'g?' ], 'e' => 'f!' ]
```
