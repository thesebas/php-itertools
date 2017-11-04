<?php

namespace thesebas\itertools;

describe('itertools', function () {

    given('exampleGenerator', function () {

        return function ($count) {
            $i = 0;
            $a = ord('a');
            do {
                yield chr($i + $a);
            } while (++$i < $count);
        };

    });

    describe('`exampleGenerator`', function () {
        it('should be typical generator', function () {
            $gen = $this->exampleGenerator;

            $actual = $gen(3);
            expect(iterator_to_array($actual))->toBe(['a', 'b', 'c']);
        });
    });

    describe('tail', function () {
        it('should show last 3 items without keys', function () {
            $gen = $this->exampleGenerator;

            $actual = tail($gen(5), 3, false);
            expect(iterator_to_array($actual))->toBe(['c', 'd', 'e']);

        });
        it('should show last 3 items with keys', function () {
            $gen = $this->exampleGenerator;

            $actual = tail($gen(5), 3, true);
            expect(iterator_to_array($actual))->toBe([2 => 'c', 3 => 'd', 4 => 'e']);

        });
    });

    describe('head', function () {
        it('should show first 3 items, then next 4 items', function () {
            $gen = $this->exampleGenerator;
            $gen10 = $gen(10);
            $actual = head($gen10, 3);
            expect(iterator_to_array($actual))->toBe(['a', 'b', 'c']);

            $actual = head($gen10, 3);
            expect(iterator_to_array($actual))->toBe(['d', 'e', 'f']);
        });
        it('should show first 3 items, then next 4 items with keys', function () {
            $gen = $this->exampleGenerator;
            $gen10 = $gen(10);
            $actual = head($gen10, 3, true);
            expect(iterator_to_array($actual))->toBe([0 => 'a', 1 => 'b', 2 => 'c']);

            $actual = head($gen10, 3, true);
            expect(iterator_to_array($actual))->toBe([3 => 'd', 4 => 'e', 5 => 'f']);
        });
    });


    describe('chain', function () {
        it('should show items from first then second generator', function () {
            $gen = $this->exampleGenerator;

            $actual = chain($gen(5), $gen(3));
            expect(iterator_to_array($actual))->toBe(['a', 'b', 'c', 'd', 'e', 'a', 'b', 'c']);
        });
        it('should show items from first then second generator with keys', function () {
            $gen = $this->exampleGenerator;

            $actual = chain($gen(5), $gen(3), true);
            expect(iterator_to_array($actual))->toBe([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd'], [4, 'e'], [0, 'a'], [1, 'b'], [2, 'c']]);
        });
    });

    describe('tee', function () {
        it('should allow to iterate over same generator independently with two subiterators', function () {

            $gen = $this->exampleGenerator;


            $source = $gen(10);

            list($left, $right) = tee($source);


            expect(iterator_to_array(head($left, 3)))->toBe(['a', 'b', 'c']);
            expect(iterator_to_array(head($right, 5)))->toBe(['a', 'b', 'c', 'd', 'e']);

            expect(iterator_to_array(head($left, 5)))->toBe(['d', 'e', 'f', 'g', 'h']);
            expect(iterator_to_array(head($right, 2)))->toBe(['f', 'g']);

            expect(iterator_to_array(head($left, 2)))->toBe(['i', 'j']);
            expect(iterator_to_array(head($right, 3)))->toBe(['h', 'i', 'j']);


        });
        it('should allow to iterate over same generator independently with two subiterators with keys', function () {

            $gen = $this->exampleGenerator;


            $source = $gen(10);

            list($left, $right) = tee($source, true);


            expect(iterator_to_array(head($left, 3, true)))->toBe([0 => 'a', 1 => 'b', 2 => 'c']);
            expect(iterator_to_array(head($right, 5, true)))->toBe([0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd', 4 => 'e']);

            expect(iterator_to_array(head($left, 5, true)))->toBe([3 => 'd', 4 => 'e', 5 => 'f', 6 => 'g', 7 => 'h']);
            expect(iterator_to_array(head($right, 2, true)))->toBe([5 => 'f', 6 => 'g']);

            expect(iterator_to_array(head($left, 2, true)))->toBe([8 => 'i', 9 => 'j']);
            expect(iterator_to_array(head($right, 3, true)))->toBe([7 => 'h', 8 => 'i', 9 => 'j']);


        });
    });

    describe('filter', function () {
        it('should filter items with odd keys and return with keys', function () {
            $gen = $this->exampleGenerator;


            $source = $gen(10);

            $actual = filter($source, function ($item, $key) {
                return $key % 2 == 1;
            }, true);
            expect(iterator_to_array($actual))->toBe([1 => 'b', 3 => 'd', 5 => 'f', 7 => 'h', 9 => 'j']);
        });
        it('should filter items with odd keys', function () {
            $gen = $this->exampleGenerator;

            $source = $gen(10);

            $actual = filter($source, function ($item, $key) {
                return $key % 2 == 1;
            });
            expect(iterator_to_array($actual))->toBe(['b', 'd', 'f', 'h', 'j']);
        });
        it('should filter items matching set', function () {
            $gen = $this->exampleGenerator;

            $source = $gen(10);
            $mySet = ['a', 'g', 'j'];
            $actual = filter($source, function ($item, $key) use ($mySet) {
                return in_array($item, $mySet);
            });
            expect(iterator_to_array($actual))->toBe($mySet);
        });
    });

    describe('skip', function () {

        it('should skip 4 items and yield rest of items', function () {
            $gen = $this->exampleGenerator;

            $source = $gen(10);

            $actual = skip($source, 4);
            expect(iterator_to_array($actual))->toBe(['e', 'f', 'g', 'h', 'i', 'j']);
        });
        it('should skip 4 items and yield rest of items with keys', function () {
            $gen = $this->exampleGenerator;

            $source = $gen(10);

            $actual = skip($source, 4, true);
            expect(iterator_to_array($actual))->toBe([4 => 'e', 5 => 'f', 6 => 'g', 7 => 'h', 8 => 'i', 9 => 'j']);
        });
    });

    describe('map', function () {

        it('should map iterator to new iterator', function () {
            $gen = $this->exampleGenerator;

            $data = $gen(10);

            $data = head($data, 3);

            $actual = map($data, function ($item, $key) {
                return "{$key} => {$item}";
            });

            expect(iterator_to_array($actual))->toBe(['0 => a', '1 => b', '2 => c']);

        });

    });
    
    describe('chunk', function () {

        it('should chunk generator into pieces, each at most 3 items', function () {
            $gen = $this->exampleGenerator;

            $source = $gen(10);
            $actual = \iterator_to_array(map(chunk($source, 3), '\\iterator_to_array'));
            expect($actual)->toBe([
                ['a', 'b', 'c'],
                ['d', 'e', 'f'],
                ['g', 'h', 'i'],
                ['j']
            ]);

        });
        it('should chunk generator into pieces, each at most 3 items with keys', function () {
            $gen = $this->exampleGenerator;

            $source = $gen(10);
            $actual = \iterator_to_array(map(chunk($source, 3, true), '\\iterator_to_array'));
            expect($actual)->toBe([
                [0 => 'a', 1 => 'b', 2 => 'c'],
                [3 => 'd', 4 => 'e', 5 => 'f'],
                [6 => 'g', 7 => 'h', 8 => 'i'],
                [9 => 'j']
            ]);

        });

    });

});

