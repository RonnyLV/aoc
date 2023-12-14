<?php

function transpose($array): array
{
    array_unshift($array, null);
    $array = call_user_func_array("array_map", $array);
    $array = array_reverse($array);
    return $array;
}

/*$test = [
    [0, 1, 2],
    [3, 4, 5],
    [6, 7, 8]
];

for($i = 0; $i < 8; $i++) {
    foreach ($test as $row) {
        echo implode(" ", $row) . "\n";
    }
    $test = transpose($test);
    echo "\n";
}

exit;*/
$sample = <<<TXT
O....#....
O.OO#....#
.....##...
OO.#O....O
.O.....O#.
O.#..O.#.#
..O..#O..O
.......O..
#....###..
#OO..#....
TXT;

$t = $sample;
$t = file_get_contents("input.txt");

$lines = explode("\n", $t);

$elementRows = [];

foreach ($lines as $line) {
    $elementRows[] = str_split($line);
}

$cache = [];

const CYCLES = 1000000000;
const CYCLE_TIMES = CYCLES * 4;
$transposedTimes = 0;
$cache = [];

$hitCache = false;
for ($transposedTime = 0; $transposedTime < CYCLE_TIMES; $transposedTime++) {
    $elementRows = transpose($elementRows);
    $transposedTimes = $transposedTime % 4;

    $sortDirection = $transposedTimes % 2 === 0 ? -1 : 1;

    foreach ($elementRows as $index => $elementRow) {
        $sortedRow = $elementRow;

        // bubble sort
        for ($i = 0; $i < count($elementRows); $i++) {
            for ($j = 0; $j < count($elementRows) - $i - 1; $j++) {
                $a = $sortedRow[$j];
                $b = $sortedRow[$j + 1];
                if ($a === "#" || $b === "#") {
                    continue;
                }
                if (($a <=> $b) === $sortDirection) {
                    $t = $a;
                    $sortedRow[$j] = $b;
                    $sortedRow[$j + 1] = $t;
                }
            }
        }
        $elementRows[$index] = $sortedRow;
    }

    if ($transposedTimes === 3) {
        echo ($transposedTime + 1) / 4 . "\n";
        $cacheKey = md5(serialize($elementRows));
        if (!$hitCache && isset($cache[$cacheKey])) {
            $hitCache = true;
            $cycleIndex = $cache[$cacheKey];
            $cycleDistance = $transposedTime - $cycleIndex;
            $cyclesTimesLeft = (CYCLE_TIMES - $transposedTime) % $cycleDistance;
            $transposedTime = CYCLE_TIMES - $cyclesTimesLeft;
        }
        $cache[$cacheKey] = $transposedTime;
    }
}

for (
    $transposedTime = 0;
    $transposedTime < 3 - $transposedTimes;
    $transposedTime++
) {
    $elementRows = transpose($elementRows);
}

$sum = 0;
foreach ($elementRows as $rowIndex => $elementRow) {
    echo implode("", $elementRow) . "\n";
    foreach ($elementRow as $element) {
        if ($element === "O") {
            $sum += count($elementRows) - $rowIndex;
        }
    }
}
var_dump($sum);

//83790