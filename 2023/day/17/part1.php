<?php

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
                $newRow = $currentRow + $direction[0];
                $newColumn = $currentColumn + $direction[1];

                if (!isset($blocks[$newRow][$newColumn])) {
                    continue;
                }

                if ($previousDirections === $direction && $previousItem["stepsInDirection"] >= 3) {
                    continue;
                }

                $distance = $blocks[$newRow][$newColumn] + $previousItem["distance"];
                $stepsInDirection = $previousDirections === $direction ? $previousItem["stepsInDirection"] + 1 : 1;
                $newNode = [
                    "direction" => $direction,
                    "stepsInDirection" => $stepsInDirection,
                    "distance" => $distance,
                ];

                if ($distance <= $distances[$newRow][$newColumn]) {
                    $distances[$newRow][$newColumn] = $distance;
                }
                $prevDistances = $previous[$newRow][$newColumn][$direction[0] . ":" . $direction[1] . ":" . $stepsInDirection] ?? null;
                if(!isset($prevDistances) || $distance < $prevDistances["distance"]) {
                    $previous[$newRow][$newColumn][$direction[0] . ":" . $direction[1] . ":" . $stepsInDirection] = $newNode;
                    if (!isset($queue[$newRow . ":" . $newColumn])) {
                        $queue[$newRow . ":" . $newColumn] = [$newRow, $newColumn];
                    }
                }
            }
        }

//        foreach ($distances as $rowIndex => $row) {
//            if ($rowIndex === $currentRow) {
//                $row[$currentColumn] = $row[$currentColumn] . "#";
//            }
//            echo implode(
//                    "",
//                    array_map(fn($cell) => str_pad($cell, 15, " ", STR_PAD_LEFT), $row)
//                ) . "\n";
//        }

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
