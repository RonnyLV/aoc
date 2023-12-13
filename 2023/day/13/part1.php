<?php

$sample = <<<TXT
#.##..##.
..#.##.#.
##......#
##......#
..#.##.#.
..##..##.
#.#.##.#.

#...##..#
#....#..#
..##..###
#####.##.
#####.##.
..##..###
#....#..#
TXT;

$t = file_get_contents("input.txt");

$patterns = explode("\n\n", $t);

$rowLines = [];
$columnLines = [];
$columns = [];

function isValid(array $lines, int $lineIndex): bool
{
    for ($i = $lineIndex, $j = $lineIndex + 1; $i >= 0; $i--, $j++) {
        $topLine = $lines[$i] ?? $lines[$j];
        $bottomLine = $lines[$j] ?? $lines[$i];
        if ($topLine !== $bottomLine) {
            return false;
        }
    }

    return true;
}

function task(array $lines, int $multiplier): int
{
    $sum = 0;
    for (
        $bottomLineIndex = count($lines) - 1;
        $bottomLineIndex > 0;
        $bottomLineIndex--
    ) {
        $topLineIndex = $bottomLineIndex - 1;
        $rowTop = $lines[$topLineIndex] ?? null;
        $rowBottom = $lines[$bottomLineIndex];
        if ($rowTop === $rowBottom) {
            if (isValid($lines, $topLineIndex)) {
                $sum = ($topLineIndex + 1) * $multiplier;
                break;
            }
        }
    }
    return $sum;
}

$sum = 0;
foreach ($patterns as $patternIndex => $pattern) {
    $patternLines = explode("\n", $pattern);
    foreach ($patternLines as $lineIndex => $patternLine) {
        $rowLines[$patternIndex][$lineIndex] = $patternLine;
        foreach (range(0, strlen($patternLine) - 1) as $charIndex) {
            if (!isset($columnLines[$patternIndex][$charIndex])) {
                $columnLines[$patternIndex][$charIndex] = "";
            }
            $columnLines[$patternIndex][$charIndex] .= $patternLine[$charIndex];
        }
    }

    $sum +=
        task($rowLines[$patternIndex], 100) +
        task($columnLines[$patternIndex], 1);
}

var_dump($sum);
