<?php

$t = file_get_contents("input.txt");

$ts = <<<'TXT'
R 6 (#70c710)
D 5 (#0dc571)
L 2 (#5713f0)
D 2 (#d2c081)
R 2 (#59c680)
D 2 (#411b91)
L 5 (#8ceee2)
U 2 (#caa173)
L 1 (#1b58a2)
U 2 (#caa171)
R 2 (#7807d2)
U 3 (#a77fa3)
L 2 (#015232)
U 2 (#7a21e3)
TXT;

$ts = <<<'TXT'
R 2 (#70c710)
D 1 (#0dc571)
L 2 (#5713f0)
U 1 (#d2c081)
TXT;

$directionMap = [
    "R" => [0, 1],
    "L" => [0, -1],
    "U" => [-1, 0],
    "D" => [1, 0],
];
$directionMap = [
    0 => $directionMap["R"],
    1 => $directionMap["D"],
    2 => $directionMap["L"],
    3 => $directionMap["U"],
];

$corners = [];

$vs = [];

$lines = explode("\n", $t);
$currentRow = 0;
$currentColumn = 0;
$vs[] = [$currentRow, $currentColumn];
$polygons = [];

$totalSteps = 0;
foreach ($lines as $line) {
    $matches = [];
    preg_match("/([RLDU]) (\d+) \((#[0-9a-f]{6})\)/", $line, $matches);
    [$_, $direction, $steps, $color] = $matches;
    $color = substr($color, 1);
    $direction = $color[5];
    $steps = hexdec(substr($color, 0, 5));
    $currentRow = $currentRow + $directionMap[$direction][0] * $steps;
    $currentColumn = $currentColumn + $directionMap[$direction][1] * $steps;
    $vs[] = [$currentRow, $currentColumn];
    $totalSteps += $steps;
}

$xs = [];
$ys = [];

foreach ($vs as $index => [$posY, $posX]) {
    $xs[] = $posX;
    $ys[] = $posY;
}

$insideBlocks = [];
$onLine = [];

function polygonArea()
{
    global $totalSteps, $xs, $ys;
    // Initialize area
    $area = 0;

    // Calculate value of shoelace formula
    for ($i = 0, $j = count($xs) - 1; $i < count($xs); $j = $i++) {
        $area += ($xs[$j] + $xs[$i]) * ($ys[$j] - $ys[$i]);
    }

    // Return absolute value
    return 1 + (abs($area) + $totalSteps) / 2;
}

var_dump(polygonArea());

//57196493937398
