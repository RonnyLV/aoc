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

$directionMap = [
    "R" => [0, 1],
    "L" => [0, -1],
    "U" => [-1, 0],
    "D" => [1, 0],
];

$corners = [];

$vs = [];

$lines = explode("\n", $t);
$currentRow = 0;
$currentColumn = 0;
$vs[] = [$currentRow, $currentColumn];
$polygons = [];
foreach ($lines as $line) {
    $matches = [];
    preg_match("/([RLDU]) (\d+) \((#[0-9a-f]{6})\)/", $line, $matches);
    [$_, $direction, $steps, $color] = $matches;
    $currentRow = $currentRow + $directionMap[$direction][0] * $steps;
    $currentColumn = $currentColumn + $directionMap[$direction][1] * $steps;
    $vs[] = [$currentRow, $currentColumn];
}

$minPosX = 100000000000000;
$minPosY = 100000000000000;
$maxPosX = 0;
$maxPosY = 0;
foreach ($vs as [$posY, $posX]) {
    $minPosX = min($minPosX, $posX);
    $minPosY = min($minPosY, $posY);
    $maxPosX = max($maxPosX, $posX);
    $maxPosY = max($maxPosY, $posY);
}

$width = $maxPosX - $minPosX + 1;
$height = $maxPosY - $minPosY + 1;

foreach ($vs as [$posY, $posX]) {
    $polygons[] = $posX - $minPosX;
    $polygons[] = $posY - $minPosY;
}

$im = imagecreate($width, $height);
// Set the background color of image
$background_color = imagecolorallocate($im, 255, 255, 255);
// Fill background with above selected color
imagefill($im, 0, 0, $background_color);
$color1 = imagecolorallocate($im, 0, 0, 0);
imagepolygon($im, $polygons, count($polygons) / 2, $color1);
imagefilledpolygon($im, $polygons, count($polygons) / 2, $color1);
imagepng($im, "image.png");

$width = imagesx($im);
$height = imagesy($im);
$colors = [];
foreach (range(0, $height - 1) as $rowI) {
    foreach (range(0, $width - 1) as $colI) {
        $colorFilled = imagecolorat($im, $colI, $rowI);
        if ($colorFilled === false) {
            continue;
        }
        if (!isset($colors[$colorFilled])) {
            $colors[$colorFilled] = 0;
        }
        $colors[$colorFilled] += 1;
    }
}

var_dump($colors[$color1]);
// 52231
