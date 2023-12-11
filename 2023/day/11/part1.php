<?php

$t = file_get_contents("input.txt");

$t = <<<TXT
...#......
.......#..
#.........
..........
......#...
.#........
.........#
..........
.......#..
#...#.....
TXT;

$map = [];

function transpose($array): array
{
    array_unshift($array, null);
    $array = call_user_func_array("array_map", $array);
    $array = array_map("array_reverse", $array);
    return $array;
}
transpose([[0, 1, 2], [3, 4, 5], [6, 7, 8]]);
function transpose_reverse($array): array
{
    array_unshift($array, null);
    $array = call_user_func_array("array_map", $array);
    $array = array_reverse($array);
    return $array;
}

$lines = explode("\n", $t);

$rowCharTypeCount = [];
$colCharTypeCount = [];
$colCount = 0;

foreach ($lines as $i => $line) {
    $map[$i] = str_split($line);
}

$rowCount = count($lines);
$colCount = count($map[0]);

foreach ($map as $i => $row) {
    foreach ($row as $j => $char) {
        if (!isset($rowCharTypeCount[$i][$char])) {
            $rowCharTypeCount[$i][$char] = 0;
        }
        if (!isset($colCharTypeCount[$j][$char])) {
            $colCharTypeCount[$j][$char] = 0;
        }
        $rowCharTypeCount[$i][$char]++;
        $colCharTypeCount[$j][$char]++;
    }
}

$expandableRows = [];
$expandableCols = [];

$expandedMap = $map;
$rowModifier = 0;

foreach ($rowCharTypeCount as $rowIndex => $rowCharTypes) {
    if (($rowCharTypes["."] ?? 0) === $colCount) {
        $expandableRows[$rowIndex] = true;
        array_splice($expandedMap, $rowIndex + $rowModifier, 0, [
            array_fill(0, $colCount, "."),
        ]);
        $rowModifier++;
    }
}

$colModifier = 0;
$expandedMap = transpose($expandedMap);

foreach ($colCharTypeCount as $colIndex => $colCharTypes) {
    if (($colCharTypes["."] ?? 0) === $rowCount) {
        $expandableCols[$colIndex] = true;
        array_splice($expandedMap, $colIndex + $colModifier, 0, [
            array_fill(0, $rowCount + $rowModifier, "."),
        ]);
        $colModifier++;
    }
}

$expandedMap = transpose_reverse($expandedMap);

function printMap(array $expandedMap): void
{
    foreach ($expandedMap as $rowIndex => $row) {
        foreach ($row as $colIndex => $char) {
            echo $char;
        }
        echo "\n";
    }
}

printMap($expandedMap);

$galaxies = [];

foreach ($expandedMap as $i => $row) {
    foreach ($row as $j => $char) {
        if ($char === "#") {
            $galaxies[] = [$i, $j];
        }
    }
}

$steps = 0;

foreach ($galaxies as $galaxy1) {
    array_shift($galaxies);
    foreach ($galaxies as $galaxy2) {
        $steps +=
            abs($galaxy1[0] - $galaxy2[0]) + abs($galaxy1[1] - $galaxy2[1]);
    }
}

var_dump($steps);
