<?php

$t = <<<'TXT'
.|...\....
|.-.\.....
.....|-...
........|.
..........
.........\
..../.\\..
.-.-/..|..
.|....-|.\
..//.|....
TXT;

$t = file_get_contents("input.txt");

$lines = explode("\n", $t);

$tiles = [];
foreach ($lines as $i => $line) {
    $tilesRow = [];
    foreach (str_split($line) as $tile) {
        $tilesRow[] = $tile;
    }
    $tiles[] = $tilesRow;
}

$energized = [];

enum Direction
{
    case up;
    case down;
    case left;
    case right;
}

$cache = [];
function glow(int $startRow, int $startColumn, Direction $currentDirection)
{
    global $tiles, $energized, $cache;
    $currentTiles = $tiles;
    $currentTiles[$startRow][$startColumn] = "X";
    $tilesStr = "";
    foreach ($currentTiles as $row) {
        $tilesStr .= implode("", $row) . "\n";
    }
    //    $energized[$startRow][$startColumn] = true;
    $cacheKey = "$startRow:$startColumn:" . $currentDirection->name;

    if (isset($cache[$cacheKey])) {
        var_dump($tilesStr);
        return;
    }

    if (
        $currentDirection === Direction::right ||
        $currentDirection === Direction::left
    ) {
        $columnModifier = $currentDirection === Direction::right ? 1 : -1;
        $rowModifier = 0;
    } else {
        $rowModifier = $currentDirection === Direction::down ? 1 : -1;
        $columnModifier = 0;
    }
    for (
        $currentColumn = $startColumn,
        $currentRow = $startRow,
        $currentTile = $tiles[$currentRow][$currentColumn] ?? null;
        isset($tiles[$currentRow][$currentColumn]);
        $currentColumn += $columnModifier,
        $currentRow += $rowModifier,
        $currentTile = $tiles[$currentRow][$currentColumn] ?? null
    ) {
        $energized[$currentRow][$currentColumn] = true;
        if (
            $currentTile === "\\" ||
            $currentTile === "/" ||
            ($rowModifier === 0 && $currentTile === "|") ||
            ($columnModifier === 0 && $currentTile === "-")
        ) {
            break;
        }
    }

    $cache[$cacheKey] = true;

    if ($currentTile === "\\") {
        match ($currentDirection) {
            Direction::right => glow(
                $currentRow + 1,
                $currentColumn,
                Direction::down
            ),
            Direction::left => glow(
                $currentRow - 1,
                $currentColumn,
                Direction::up
            ),
            Direction::up => glow(
                $currentRow,
                $currentColumn - 1,
                Direction::left
            ),
            Direction::down => glow(
                $currentRow,
                $currentColumn + 1,
                Direction::right
            ),
        };
    } elseif ($currentTile === "/") {
        match ($currentDirection) {
            Direction::right => glow(
                $currentRow - 1,
                $currentColumn,
                Direction::up
            ),
            Direction::left => glow(
                $currentRow + 1,
                $currentColumn,
                Direction::down
            ),
            Direction::up => glow(
                $currentRow,
                $currentColumn + 1,
                Direction::right
            ),
            Direction::down => glow(
                $currentRow,
                $currentColumn - 1,
                Direction::left
            ),
        };
    } elseif ($currentTile === "|") {
        if (
            $currentDirection === Direction::right ||
            $currentDirection === Direction::left
        ) {
            glow($currentRow + 1, $currentColumn, Direction::down);
            glow($currentRow - 1, $currentColumn, Direction::up);
        }
    } elseif ($currentTile === "-") {
        if (
            $currentDirection === Direction::up ||
            $currentDirection === Direction::down
        ) {
            glow($currentRow, $currentColumn + 1, Direction::right);
            glow($currentRow, $currentColumn - 1, Direction::left);
        }
    }
}

glow(0, 0, Direction::right);

$sum = 0;
foreach ($energized as $row) {
    $sum += count($row);
}
var_dump($sum);

/*foreach($tiles as $rowIndex=> $tileRow){
    foreach($energized[$rowIndex] ?? [] as $cellIndex => $energizedCell){
        $tileRow[$cellIndex] = "#";
    }
    $str = implode("", $tileRow);
    echo $str . "\n";
}*/

//8489
