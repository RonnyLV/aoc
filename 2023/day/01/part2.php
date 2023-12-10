<?php
$t = file_get_contents("input.txt");

$sum = 0;

$map = [
    "one" => 1,
    "two" => 2,
    "three" => 3,
    "four" => 4,
    "five" => 5,
    "six" => 6,
    "seven" => 7,
    "eight" => 8,
    "nine" => 9,
];

foreach (explode("\n", $t) as $line) {
    $matches = [];
    $firstVar = null;
    foreach (range(0, strlen($line)) as $i) {
        foreach ($map as $key => $value) {
            $newLine = substr($line, $i);
            if (str_starts_with($newLine, $key)) {
                $firstVar = $key;
                break 2;
            } elseif (str_starts_with($newLine, $value)) {
                $firstVar = $value;
                break 2;
            }
        }
    }
    $first = $map[$firstVar] ?? $firstVar;

    $lastVar = null;
    foreach (range(strlen($line), 0) as $i) {
        foreach ($map as $key => $value) {
            $newLine = substr($line, $i);
            if (str_starts_with($newLine, $key)) {
                $lastVar = $key;
                break 2;
            } elseif (str_starts_with($newLine, $value)) {
                $lastVar = $value;
                break 2;
            }
        }
    }

    $last = $map[$lastVar] ?? $lastVar;
    $lineNo = (int) "$first$last";
    $sum += $lineNo;
}

echo $sum;
