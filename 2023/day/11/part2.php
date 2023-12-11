<?php

const EMPTY_GALAXY_MODIFIER = 1000000;
$t = file_get_contents("input.txt");

$ts = <<<TXT
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

foreach ($rowCharTypeCount as $rowIndex => $rowCharTypes) {
    if (($rowCharTypes["."] ?? 0) === $colCount) {
        $expandableRows[$rowIndex] = true;
    }
}

foreach ($colCharTypeCount as $colIndex => $colCharTypes) {
    if (($colCharTypes["."] ?? 0) === $rowCount) {
        $expandableCols[$colIndex] = true;
    }
}

$galaxies = [];

foreach ($map as $i => $row) {
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
        $stepsY = -1;
        foreach (range($galaxy1[0], $galaxy2[0]) as $rowStep) {
            if ($expandableRows[$rowStep] ?? false) {
                $stepsY += EMPTY_GALAXY_MODIFIER;
            } else {
                $stepsY += 1;
            }
        }
        $stepsX = -1;
        foreach (range($galaxy1[1], $galaxy2[1]) as $colStep) {
            if ($expandableCols[$colStep] ?? false) {
                $stepsX += EMPTY_GALAXY_MODIFIER;
            } else {
                $stepsX += 1;
            }
        }
        $steps += $stepsY + $stepsX;
    }
}

var_dump($steps);
