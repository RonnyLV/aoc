<?php

$cache = [];

function scanLine(
    int    $cellIndex,
    int    $occurrenceIndex,
    int    $current,
    string $cellsString,
    string $newString,
    array  $occurrences,
    int    $occurrenceCount,
    int    $cellStringLength
): int
{
    global $cache;
    $cacheKey = "{$cellIndex}:{$occurrenceIndex}:{$current}:{$cellsString}:" . implode(".", $occurrences);

    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }

    $result = 0;
    $currentOccurrence = $occurrences[$occurrenceIndex] ?? null;
    $inprogressString = $cellsString;
    $inprogressString[$cellIndex] = "_";

    if ($cellIndex === $cellStringLength) {
        // trailing . or end of sequence
        if (
            ($occurrenceIndex === ($occurrenceCount - 1) &&
                $currentOccurrence === $current) ||
            ($occurrenceIndex >= $occurrenceCount && $current === 0)
        ) {
            $cache[$cacheKey] = 1;
            // valid arrangement found
            return 1;
        } else {
            $cache[$cacheKey] = 0;
            return 0;
        }
    }

    $currentCell = $cellsString[$cellIndex];

    if ($currentCell === "#" || $currentCell === "?") {
        $newString[$cellIndex] = "#";
        $result += scanLine(
            cellIndex: $cellIndex + 1,
            occurrenceIndex: $occurrenceIndex,
            current: $current + 1,
            cellsString: $cellsString,
            newString: $newString,
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
                cellsString: $cellsString,
                newString: $newString,
                occurrences: $occurrences,
                occurrenceCount: $occurrenceCount,
                cellStringLength: $cellStringLength
            );
        } elseif ($currentOccurrence === $current) {
            // stay on same occurrence if not started
            $result += scanLine(
                cellIndex: $cellIndex + 1,
                occurrenceIndex: $occurrenceIndex + 1,
                current: 0,
                cellsString: $cellsString,
                newString: $newString,
                occurrences: $occurrences,
                occurrenceCount: $occurrenceCount,
                cellStringLength: $cellStringLength
            );
        }
    }

    $cache[$cacheKey] = $result;
    return $result;
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
    $explodedLine = explode(" ", $line);
    $cellOccurrences = array_map("intval", explode(",", $explodedLine[1]));

    $copiedItems = [];
    $copiedCellOccurrences = [];

    foreach (range(0, 4) as $range) {
        $copiedItems[] = $explodedLine[0];
        $copiedCellOccurrences = [
            ...$copiedCellOccurrences,
            ...$cellOccurrences,
        ];
    }

    $copiedItemsStr = implode("?", $copiedItems);

    $result = scanLine(
        cellIndex: 0,
        occurrenceIndex: 0,
        current: 0,
        cellsString: $copiedItemsStr,
        newString: $copiedItemsStr,
        occurrences: $copiedCellOccurrences,
        occurrenceCount: count($copiedCellOccurrences),
        cellStringLength: strlen($copiedItemsStr)
    );

    echo $line . " :::: $result\n";
    $sum += $result;
}

echo "The result: $sum\n";

//15690445806391
//
