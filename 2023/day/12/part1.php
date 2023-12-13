<?php

function scanLine(
    int $cellIndex,
    int $occurrenceIndex,
    int $current,
    string $cellsString,
    array $occurrences,
    int $occurrenceCount,
    int $cellStringLength
): int {
    $result = 0;
    $currentOccurrence = $occurrences[$occurrenceIndex] ?? 0;
    $inprogressString = $cellsString;
    $inprogressString[$cellIndex] = "_";

    if ($cellIndex === $cellStringLength) {
        if (
            $occurrenceIndex + 1 >= $occurrenceCount &&
            $currentOccurrence === $current
        ) {
            // valid arrangement found
            return 1;
        } else {
            return 0;
        }
    }

    $currentCell = $cellsString[$cellIndex];
    $newString = $cellsString;

    if ($currentCell === "#" || $currentCell === "?") {
        $newString[$cellIndex] = "#";
        $result += scanLine(
            cellIndex: $cellIndex + 1,
            occurrenceIndex: $occurrenceIndex,
            current: $current + 1,
            cellsString: $newString,
            occurrences: $occurrences,
            occurrenceCount: $occurrenceCount,
            cellStringLength: $cellStringLength
        );
    }

    if ($currentCell === "." || $currentCell === "?") {
        $newString[$cellIndex] = ".";
        if ($current === 0) {
            // next occurrence if already started
            $result += scanLine(
                cellIndex: $cellIndex + 1,
                occurrenceIndex: $occurrenceIndex,
                current: 0,
                cellsString: $newString,
                occurrences: $occurrences,
                occurrenceCount: $occurrenceCount,
                cellStringLength: $cellStringLength
            );
        } elseif ($current === $currentOccurrence) {
            // stay on same occurrence if not started
            $result += scanLine(
                cellIndex: $cellIndex + 1,
                occurrenceIndex: $occurrenceIndex + 1,
                current: 0,
                cellsString: $newString,
                occurrences: $occurrences,
                occurrenceCount: $occurrenceCount,
                cellStringLength: $cellStringLength
            );
        }
    }

    return $result;
}

function countArrangements(string $line): int
{
    $explodedLine = explode(" ", $line);
    $items = str_split($explodedLine[0]);
    $cellOccurrences = array_map("intval", explode(",", $explodedLine[1]));
    return scanLine(
        0,
        0,
        0,
        $explodedLine[0],
        $cellOccurrences,
        count($cellOccurrences),
        strlen($explodedLine[0])
    );
}

const TEST_RESULTS = [
    "???.### 1,1,3" => 1,
    ".??..??...?##. 1,1,3" => 4,
    "?#?#?#?#?#?#?#? 1,3,1,6" => 1,
    "????.#...#... 4,1,1" => 1,
    "????.######..#####. 1,6,5" => 4,
    "?###???????? 3,2,1" => 10,
];

$sample = <<<TXT
???.### 1,1,3
.??..??...?##. 1,1,3
?#?#?#?#?#?#?#? 1,3,1,6
????.#...#... 4,1,1
????.######..#####. 1,6,5
?###???????? 3,2,1
TXT;

$t = $sample;
$t = file_get_contents("input.txt");

$lines = explode("\n", $t);
$sum = 0;
foreach ($lines as $line) {
    $result = countArrangements($line);
    if (isset(TEST_RESULTS[$line])) {
        if (TEST_RESULTS[$line] !== $result) {
            echo "ERROR: $line should be " .
                TEST_RESULTS[$line] .
                " but got $result\n";
        } else {
            echo "OK: $line\n";
        }
    }
    $sum += $result;
}

echo "The result: $sum\n";

//7599
