<?php

const TOP = [-1, 0];
const LEFT = [0, -1];
const RIGHT = [0, 1];
const BOTTOM = [1, 0];

$t = file_get_contents("input.txt");

$ts = <<<TXT
7-F7-
.FJ|7
SJLL7
|F--J
LJ.LJ
TXT;

$ts = <<<TXT
FF7FSF7F7F7F7F7F---7
L|LJ||||||||||||F--J
FL-7LJLJ||||||LJL-77
F--JF--7||LJLJIF7FJ-
L---JF-JLJIIIIFJLJJ7
|F|F-JF---7IIIL7L|7|
|FFJF7L7F-JF7IIL---7
7-L-JL7||F7|L7F-7F7|
L.L7LFJ|||||FJL7||LJ
L7JLJL-JLJLJL--JLJ.L
TXT;

$ts = <<<TXT
...........
.S-------7.
.|F-----7|.
.||.....||.
.||.....||.
.|L-7.F-J|.
.|..|.|..|.
.L--J.L--J.
...........
TXT;

$ts = <<<TXT
.F----7F7F7F7F-7....
.|F--7||||||||FJ....
.||.FJ||||||||L7....
FJL7L7LJLJ||LJ.L-7..
L--J.L7...LJS7F-7L7.
....F-J..F7FJ|L7L7L7
....L7.F7||L7|.L7L7|
.....|FJLJ|FJ|F7|.LJ
....FJL-7.||.||||...
....L---J.LJ.LJLJ...
TXT;

$map = [];
$alreadyVisited = [];

$maxRows = null;
$maxColumns = null;

const POS_MODIFIERS = [
    "J" => [LEFT, TOP],
    "L" => [RIGHT, TOP],
    "7" => [LEFT, BOTTOM],
    "F" => [RIGHT, BOTTOM],
    "-" => [LEFT, RIGHT],
    "|" => [TOP, BOTTOM],
    "S" => [LEFT, RIGHT, TOP, BOTTOM],
];

$theLoop = [];
$polygon = [];

function map(array $currentPos, array $previousPos, int $moveIndex)
{
    global $map, $maxRows, $maxColumns, $theLoop, $polygon;
    while (true) {
        [$currentRow, $currentColumn] = $currentPos;
        $currentConnector = $map[$currentRow][$currentColumn];
        foreach (POS_MODIFIERS[$currentConnector] as $nextPosModifier) {
            $nextPos = [
                $currentPos[0] + $nextPosModifier[0],
                $currentPos[1] + $nextPosModifier[1],
            ];
            [$nextRow, $nextColumn] = $nextPos;
            if (
                $previousPos === $nextPos ||
                $nextRow < 0 ||
                $nextColumn < 0 ||
                $nextRow > $maxRows - 1 ||
                $nextColumn > $maxColumns - 1
            ) {
                continue;
            }

            $nextRowCell = $map[$nextRow][$nextColumn];

            if ($nextRowCell === "S") {
                return ($moveIndex + 1) / 2;
            }

            if ($nextRowCell == "S" || $nextRowCell == ".") {
                continue;
            }

            if ($nextPosModifier === TOP) {
                if (
                    !(
                        $nextRowCell == "7" ||
                        $nextRowCell == "|" ||
                        $nextRowCell == "F"
                    )
                ) {
                    continue;
                }
                $dir = "top";
            } elseif ($nextPosModifier === BOTTOM) {
                if (
                    !(
                        $nextRowCell == "J" ||
                        $nextRowCell == "|" ||
                        $nextRowCell == "L"
                    )
                ) {
                    continue;
                }
                $dir = "bottom";
            } elseif ($nextPosModifier === LEFT) {
                if (
                    !(
                        $nextRowCell == "L" ||
                        $nextRowCell == "-" ||
                        $nextRowCell == "F"
                    )
                ) {
                    continue;
                }
                $dir = "left";
            } elseif ($nextPosModifier === RIGHT) {
                if (
                    !(
                        $nextRowCell == "J" ||
                        $nextRowCell == "-" ||
                        $nextRowCell == "7"
                    )
                ) {
                    continue;
                }
                $dir = "right";
            }

            $moveIndex += 1;
            $theLoop[$currentPos[0]][$currentPos[1]] = true;
            $polygon[] = $currentPos[1];
            $polygon[] = $currentPos[0];
            $previousPos = $currentPos;
            $currentPos = $nextPos;
            continue 2;
        }
    }
    return null;
}

$startColumn = null;
$startRow = null;

$lines = explode("\n", $t);

$maxRows = count($lines);
$maxColumns = null;

foreach ($lines as $rowIndex => $line) {
    $map[$rowIndex] = str_split($line);
    $maxColumns = count($map[$rowIndex]);
    if (in_array("S", $map[$rowIndex])) {
        $startColumn = array_search("S", $map[$rowIndex]);
        $startRow = $rowIndex;
    }
}

$result = map([$startRow, $startColumn], [$startRow, $startColumn], 0, []);
var_dump($result);

$filledIm = imagecreatetruecolor($maxColumns, $maxRows);
$notFilledIm = imagecreatetruecolor($maxColumns, $maxRows);
$resultIm = imagecreatetruecolor($maxColumns, $maxRows);

// Set the background color of image
$background_color = imagecolorallocate($notFilledIm, 0, 153, 0);
// Fill background with above selected color
imagefill($notFilledIm, 0, 0, $background_color);
// Allocate a color for the polygon
$image_color = imagecolorallocate($notFilledIm, 255, 255, 255);
// Draw the polygon
imagepolygon($notFilledIm, $polygon, count($polygon) / 2, $image_color);
imagepng($notFilledIm, __DIR__ . "/notFilled.png");

// Set the background color of image
$background_color = imagecolorallocate($filledIm, 0, 153, 0);
// Fill background with above selected color
imagefill($filledIm, 0, 0, $background_color);
// Allocate a color for the polygon
$image_color = imagecolorallocate($filledIm, 255, 255, 255);
// Draw the polygon
imagefilledpolygon($filledIm, $polygon, count($polygon) / 2, $image_color);
imagepng($filledIm, __DIR__ . "/filled.png");

$width = imagesx($filledIm);
$height = imagesy($filledIm);

$diffsCount = 0;
foreach (range(0, $height - 1) as $rowI) {
    foreach (range(0, $width - 1) as $colI) {
        if (isset($theLoop[$rowI][$colI])) {
            imagesetpixel(
                $resultIm,
                $colI,
                $rowI,
                imagecolorallocate($resultIm, 255, 0, 0)
            );
            continue;
        }
        $colorFilled = imagecolorat($filledIm, $colI, $rowI);
        $colorNotFilled = imagecolorat($notFilledIm, $colI, $rowI);
        if ($colorFilled !== $colorNotFilled) {
            $diffsCount++;
            imagesetpixel(
                $resultIm,
                $colI,
                $rowI,
                imagecolorallocate($resultIm, 0, 255, 0)
            );
        }
    }
}
imagepng($resultIm, __DIR__ . "/result.png");

var_dump($diffsCount);

//339
