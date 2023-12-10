<?php

$t = file_get_contents("input.txt");

$ts = <<<TXT
0 3 6 9 12 15
1 3 6 10 15 21
10 13 16 21 30 45
TXT;

$lines = explode("\n", $t);
$diffs = [];

function getUntilAllZero(array $numbers): array
{
    $allZeros = true;
    $diffs = [];
    for ($i = 0; $i < count($numbers) - 1; $i++) {
        $diff = $numbers[$i + 1] - $numbers[$i];
        $diffs[] = $diff;
        if ($diff !== 0) {
            $allZeros = false;
        }
    }
    if (!$allZeros) {
        return [$diffs, ...getUntilAllZero($diffs)];
    } else {
        return [$diffs];
    }
}

function getNext(array $reversedHistory, int $i): array
{
    $bottomRow = array_key_last($reversedHistory[$i]);
    $topRow = array_key_last($reversedHistory[$i - 1]);

    $topRightRow =
        $reversedHistory[$i][$bottomRow] + $reversedHistory[$i - 1][$topRow];
    $reversedHistory[$i - 1][] = $topRightRow;

    if ($i - 1 === 0) {
        return $reversedHistory;
    } else {
        return getNext($reversedHistory, $i - 1);
    }
}

function getPrevious(array $reversedHistory, int $i): array
{
    $bottomRow = array_key_first($reversedHistory[$i]);
    $topRow = array_key_first($reversedHistory[$i - 1]);

    $topLeftRow =
        $reversedHistory[$i][$bottomRow] - $reversedHistory[$i - 1][$topRow];
    $reversedHistory[$i - 1] = [-$topLeftRow, ...$reversedHistory[$i - 1]];

    if ($i - 1 === 0) {
        return $reversedHistory;
    } else {
        return getPrevious($reversedHistory, $i - 1);
    }
}

$history = [];
$nexts = [];
foreach ($lines as $line) {
    $numbers = array_map("intval", preg_split("/\s+/", $line));
    $historyItem = [$numbers, ...getUntilAllZero($numbers)];
    $history[] = $historyItem;
    $next = getPrevious($historyItem, count($historyItem) - 1);
    $nexts[] = $next;
}

$sum = 0;

foreach ($history as $historyItem => $h) {
    $rowCount = count($h);
    foreach ($h as $i => $row) {
        echo str_repeat(" ", 2 * $i) . implode(" ", $row) . "\n";
    }
    foreach ($nexts[$historyItem] as $i => $row) {
        echo str_repeat(" ", 2 * $i) . implode(" ", $row) . "\n";
    }

    $sum += $nexts[$historyItem][0][array_key_first($nexts[$historyItem][0])];
}

var_dump($sum);
