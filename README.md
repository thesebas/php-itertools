# thesebas/itertools

Tools to work with iterators. ![BUILD STATUS](https://api.travis-ci.org/thesebas/php-itertools.svg?branch=master) [![Coverage Status](https://coveralls.io/repos/github/thesebas/php-itertools/badge.svg)](https://coveralls.io/github/thesebas/php-itertools)

## Installation

`composer require thesebas/itertools`

## Usage
Lest assume we have some generator that yields letters.
```php
expect(iterator_to_array(gen(3)))->toBe(['a', 'b', 'c']);
```

###tail

Skip all but `n` items from Iterator. 

```php
$actual = tail(gen(5), 3, false);
expect(iterator_to_array($actual))->toBe(['c', 'd', 'e']);
```

###head

Iterator over `n` first items.

```php
$actual = head(gen(10), 3);
expect(iterator_to_array($actual))->toBe(['a', 'b', 'c']);
```

###skip

Skip `n` items and iterate over rest.

```php

$actual = skip(gen(10), 4);
expect(iterator_to_array($actual))->toBe(['e', 'f', 'g', 'h', 'i', 'j']);
```
###tee

Split Iterator to two independent Iterators (with internal buffering).

```php
list($left, $right) = tee(gen(10));


expect(iterator_to_array(head($left, 3)))->toBe(['a', 'b', 'c']);
expect(iterator_to_array(head($right, 5)))->toBe(['a', 'b', 'c', 'd', 'e']);

expect(iterator_to_array(head($left, 5)))->toBe(['d', 'e', 'f', 'g', 'h']);
expect(iterator_to_array(head($right, 2)))->toBe(['f', 'g']);

expect(iterator_to_array(head($left, 2)))->toBe(['i', 'j']);
expect(iterator_to_array(head($right, 3)))->toBe(['h', 'i', 'j']);
```

###chain

Iterate over first, then second, third...

```php
$actual = chain(gen(5), gen(3));
expect(iterator_to_array($actual))->toBe(['a', 'b', 'c', 'd', 'e', 'a', 'b', 'c']);
```
###filter

Iterate ovel all but yield only filtered items.

```php
$source = gen(10);

$actual = filter($source, function ($item, $key) {
    return $key % 2 == 1;
});
expect(iterator_to_array($actual))->toBe(['b', 'd', 'f', 'h', 'j']);
```
###map

Return new Iterator with mapped values

```php

$actual = map(gen(3), function ($item, $key) {
    return "item {$key}: {$item}";
});

expect(iterator_to_array($actual))->toBe(['item 0: a', 'item 1: b', 'item 2: c']);
```
###chunk

Return iterator of chunk iterators.

```php
$actual = \iterator_to_array(map(chunk(gen(10), 3), '\\iterator_to_array'));
expect($actual)->toBe([
    ['a', 'b', 'c'],
    ['d', 'e', 'f'],
    ['g', 'h', 'i'],
    ['j']
]);
```