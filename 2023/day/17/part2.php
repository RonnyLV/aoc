<?php

const MAX_STEPS = 10;
const MIN_STEPS = 4;
$t = file_get_contents("input.txt");

$ts = <<<'TXT'
2413432311323
3215453535623
3255245654254
3446585845452
4546657867536
1438598798454
4457876987766
3637877979653
4654967986887
4564679986453
1224686865563
2546548887735
4322674655533
TXT;

$ts = <<<'TXT'
111111111111
999999999991
999999999991
999999999991
999999999991
TXT;

$ts = <<<'TXT'
1111111111
2222222222
3333333333
4444444444
5555555555
TXT;


function dp2()
{
    global $blocks;
    $queue = [];
    $distances = [];
    foreach ($blocks as $currentRow => $blockRow) {
        foreach ($blockRow as $currentColumn => $cell) {
            $queue[$currentRow . ":" . $currentColumn] = [
                $currentRow,
                $currentColumn,
            ];
            $distances[$currentRow][$currentColumn] = 100000000;
            $previous[$currentRow][$currentColumn] = [];
        }
    }
    $distances[0][0] = 0;
    $previous[0][0] = [
        [
            "direction" => [0, 1],
            "stepsInDirection" => 0,
            "distance" => 0,
        ],
        [
            "direction" => [1, 0],
            "stepsInDirection" => 0,
            "distance" => 0,
        ]
    ];

    while (!empty($queue)) {
        $minDistance = PHP_INT_MAX;
        $minQueueKey = null;
        foreach ($queue as $key => $value) {
            $distance = $distances[$value[0]][$value[1]];
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $minQueueKey = $key;
            }
        }
        $queueItem = $queue[$minQueueKey];
        unset($queue[$minQueueKey]);

        $currentRow = $queueItem[0];
        $currentColumn = $queueItem[1];

        foreach ($previous[$currentRow][$currentColumn] as $previousItem) {
            $previousDirections = $previousItem["direction"];
            $directions = [
                $previousDirections,
                [$previousDirections[1], $previousDirections[0]],
                [-$previousDirections[1], -$previousDirections[0]],
            ];
            foreach ($directions as $direction) {
                $dirY = $direction[0];
                $dirX = $direction[1];
                $newRow = $currentRow + $dirY;
                $newColumn = $currentColumn + $dirX;

                if (!isset($blocks[$newRow][$newColumn])) {
                    continue;
                }

                if ($previousDirections === $direction && $previousItem["stepsInDirection"] >= MAX_STEPS) {
                    continue;
                }
                if ($previousDirections !== $direction && $previousItem["stepsInDirection"] < MIN_STEPS) {
                    continue;
                }

                $distance = $blocks[$newRow][$newColumn] + $previousItem["distance"];
                $stepsInDirection = $previousDirections === $direction ? $previousItem["stepsInDirection"] + 1 : 1;
                $newNode = [
                    "direction" => $direction,
                    "stepsInDirection" => $stepsInDirection,
                    "distance" => $distance,
                ];

                if ($stepsInDirection >= MIN_STEPS && $distance <= $distances[$newRow][$newColumn]) {
                    $distances[$newRow][$newColumn] = $distance;
                }
                $prevDistances = $previous[$newRow][$newColumn][$dirY . ":" . $dirX . ":" . $stepsInDirection] ?? null;
                if (!isset($prevDistances) || $distance < $prevDistances["distance"]) {
                    $previous[$newRow][$newColumn][$dirY . ":" . $dirX . ":" . $stepsInDirection] = $newNode;
                    if (!isset($queue[$newRow . ":" . $newColumn])) {
                        $queue[$newRow . ":" . $newColumn] = [$newRow, $newColumn];
                    }
                }
            }
        }

        /*foreach ($distances as $rowIndex => $row) {
            foreach ($row as $currentColumn2 => $cell) {
                if ($rowIndex === $currentRow && $currentColumn2 === $currentColumn) {
                    $row[$currentColumn2] = $row[$currentColumn2] . "#" . $blocks[$rowIndex][$currentColumn2];
                }
                $row[$currentColumn2] = $row[$currentColumn2] . "#" . $blocks[$rowIndex][$currentColumn2];
            }
            echo implode(
                    "",
                    array_map(fn($cell) => str_pad($cell, 50, " ", STR_PAD_LEFT), $row)
                ) . "\n";
        }*/

        echo "Items left: " . count($queue) . "\n";
    }

//    foreach (array_slice($distances, $maxRow - $maxRow, null, true) as $rowIndex => $row) {
//        foreach ($row as $currentColumn => $cell) {
//            $row[$currentColumn] = $row[$currentColumn] . "#" . $blocks[$rowIndex][$currentColumn];
//        }
//        $row = array_slice($row, $maxColumn - $maxColumn);
//        echo implode(
//                "",
//                array_map(fn($cell) => str_pad($cell, 50, " ", STR_PAD_LEFT), $row)
//            ) . "\n";
//    }

    return $distances;
}

$blocks = [];

$lines = explode("\n", $t);
foreach ($lines as $index => $blockRowStr) {
    $blockRow = array_map("intval", str_split($blockRowStr));
    $blocks[] = $blockRow;
}

$endRow = count($blocks) - 1;
$endColumn = count($blocks[0]) - 1;

$distances = dp2();

var_dump($distances[count($distances) - 1][count($distances[0]) - 1]);
