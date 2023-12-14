<?php

function transpose($array): array
{
    array_unshift($array, null);
    $array = call_user_func_array("array_map", $array);
    return $array;
}

function transpose_reverse($array): array
{
    array_unshift($array, null);
    $array = call_user_func_array("array_map", $array);
    return $array;
}

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

$elementRows = transpose($elementRows);
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
            if (($b <=> $a) === 1) {
                $t = $a;
                $sortedRow[$j] = $b;
                $sortedRow[$j + 1] = $t;
            }
        }
    }
    $elementRows[$index] = $sortedRow;
}
$elementRows = transpose_reverse($elementRows);

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

//103614
