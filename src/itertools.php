<?php

namespace thesebas\itertools;

/**
 * @param \Iterator $iterator
 * @param int $count
 * @param bool $useKeys
 * @return \Generator
 */
function tail($iterator, $count = 1, $useKeys = false) {
    assert($iterator instanceof \Iterator);

    $buff = [];
    while ($iterator->valid()) {
        $buff[] = [$iterator->key(), $iterator->current()];
        $iterator->next();
        if (count($buff) > $count) {
            \array_shift($buff);
        }
    }

    foreach ($buff as list($key, $item)) {
        if ($useKeys) {
            yield $key => $item;
        } else {
            yield $item;
        }
    }
}

/**
 * @param \Iterator $iterator
 * @param int $count
 * @param bool $useKeys
 * @return \Generator
 */
function head($iterator, $count = 1, $useKeys = false) {
    assert($iterator instanceof \Iterator);
    while ($iterator->valid() && $count > 0) {
        $val = $iterator->current();
        if ($useKeys) {
            $key = $iterator->key();
            yield $key => $val;
        } else {
            yield $val;
        }
        $count -= 1;
        $iterator->next();
    }
}

/**
 * @param \Iterator $iterator
 * @param int $count
 * @param bool $useKeys
 * @return \Generator
 */
function skip($iterator, $count = 1, $useKeys = false) {
    assert($iterator instanceof \Iterator);

    while ($iterator->valid() && $count > 0) {
        $count -= 1;
        $iterator->next();
    }
    while ($iterator->valid()) {
        $val = $iterator->current();
        if ($useKeys) {
            $key = $iterator->key();
            yield $key => $val;
        } else {
            yield $val;
        }
        $iterator->next();
    }
}

/**
 * @param \Iterator $iterator
 * @param bool $useKeys
 * @return \Generator[]
 */
function tee($iterator, $useKeys = false) {
    assert($iterator instanceof \Iterator);
    $buff = [];
    $leftPos = 0;
    $rightPos = 0;

    $left = function () use (&$buff, &$iterator, &$leftPos, &$rightPos, $useKeys) {
        while ($iterator->valid() || (!empty($buff) && ($leftPos < $rightPos))) {
            if ($leftPos >= $rightPos) {
                $val = $useKeys ? [$iterator->key(), $iterator->current()] : $iterator->current();
                $buff[] = $val;
                $iterator->next();
            } else {
                $val = array_shift($buff);
            }
            $leftPos += 1;
            if ($useKeys) {
                list($key, $_val) = $val;
                yield $key => $_val;
            } else {
                yield $val;
            }

        }
    };
    $right = function () use (&$buff, &$iterator, &$leftPos, &$rightPos, $useKeys) {
        while ($iterator->valid() || (!empty($buff) && ($rightPos < $leftPos))) {
            if ($rightPos >= $leftPos) {
                $val = $useKeys ? [$iterator->key(), $iterator->current()] : $iterator->current();
                $buff[] = $val;
                $iterator->next();
            } else {
                $val = array_shift($buff);
            }
            $rightPos += 1;
            if ($useKeys) {
                list($key, $_val) = $val;
                yield $key => $_val;
            } else {
                yield $val;
            }
        }
    };

    return [
        $left(),
        $right(),
//        function () use (&$buff) {
//            return $buff;
//        }
    ];
}

/**
 * @param \Iterator $iterator1
 * @param \Iterator $iterator2
 * @param bool $useKeys
 * @return \Generator
 */
function chain($iterator1, $iterator2, $useKeys = false) {
    $args = func_get_args();
    if (is_bool($args[count($args) - 1])) {
        $useKeys = array_pop($args);
    } else {
        $useKeys = false;
    }
    foreach ($args as $iterator) {
        assert($iterator instanceof \Iterator);
        while ($iterator->valid()) {
            $value = $iterator->current();
            if ($useKeys) {
                $key = $iterator->key();
                yield [$key, $value];
            } else {
                yield $value;
            }
            $iterator->next();
        }
    }
}

/**
 * @param \Iterator $iterator
 * @param \Callable $predicate
 * @param bool $useKeys
 * @return \Generator
 */
function filter($iterator, $predicate, $useKeys = false) {
    assert($iterator instanceof \Iterator);
    assert(is_callable($predicate));

    while ($iterator->valid()) {
        $value = $iterator->current();
        $key = $iterator->key();
        $pred = $predicate($value, $key);
        if ($pred) {
            if ($useKeys) {
                yield $key => $value;
            } else {
                yield $value;
            }
        }
        $iterator->next();
    }
}

function map($iterator, $callable, $useKeys = false) {
    assert($iterator instanceof \Iterator);
    assert(is_callable($callable));
    while ($iterator->valid()) {
        list($key, $value) = [$iterator->key(), $iterator->current()];
        $value = $callable($value, $key);
        if ($useKeys) {
            yield $key => $value;
        } else {
            yield $value;
        }
        $iterator->next();
    }


}

function chunk($iterator, $chunkSize, $useKeys = false) {
    assert($iterator instanceof \Iterator);

    $chunkNo = 0;
    while ($iterator->valid()) {
        yield $chunkNo++ => head($iterator, $chunkSize, $useKeys);
    }
}