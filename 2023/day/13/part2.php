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
$st = $sample;

$patterns = explode("\n\n", $t);

//$patterns = [$patterns[1]];

$rowLines = [];
$columnLines = [];
$columns = [];

function isValid(array $lines, int $lineIndex): bool
{
    for (
        $i = $lineIndex, $j = $lineIndex + 1;
        $i > $lineIndex - (count($lines) - 1) / 2 ||
        $j < $lineIndex + (count($lines) + 1) / 2;
        $i--, $j++
    ) {
        $topLine = $lines[$i] ?? $lines[$j];
        $bottomLine = $lines[$j] ?? $lines[$i];

        if ($topLine !== $bottomLine) {
            return false;
        }
    }

    return true;
}

function task(array $lines, int $multiplier): ?int
{
    $sum = 0;

    for ($i = 0; $i < count($lines); $i++) {
        for ($j = 0; $j < strlen($lines[$i]); $j++) {
            $newLines = $lines;
            $newLines[$i][$j] = $newLines[$i][$j] === "#" ? "." : "#";
            for (
                $bottomLineIndex = 1;
                $bottomLineIndex < count($newLines);
                $bottomLineIndex++
            ) {
                $topLineIndex = $bottomLineIndex - 1;
                $rowTop = $newLines[$topLineIndex];
                $rowBottom = $newLines[$bottomLineIndex];
                if ($rowTop === $rowBottom) {
                    if (
                        isValid($newLines, $topLineIndex) &&
                        !isValid($lines, $topLineIndex)
                    ) {
                        $sum = max($sum, $bottomLineIndex * $multiplier);
                        break 3;
                    }
                }
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
        task($columnLines[$patternIndex], 1) +
        task($rowLines[$patternIndex], 100);
}

var_dump($sum);
//12320
//32069
