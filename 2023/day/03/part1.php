<?php

$t = file_get_contents("input.txt");

$sample = <<<TEXT
467..114..
...*......
..35..633.
......#...
617*......
.....+.58.
..592.....
......755.
...$.*....
.664.598..
TEXT;

$symbols = [];
foreach (explode("\n", $t) as $line => $text) {
    $matches = [];
    preg_match_all('~[#%&*+\-/=@$]~', $text, $matches, PREG_OFFSET_CAPTURE);
    foreach ($matches[0] as $match) {
        $symbols[$line][$match[1]] = true;
    }
}

$sum = 0;
$numbers = [];
foreach (explode("\n", $t) as $line => $text) {
    $isAdjacent = false;
    $numberBuffer = "";
    foreach (str_split($text) as $pos => $char) {
        if (is_numeric($char)) {
            $numberBuffer .= $char;
            if (!$isAdjacent) {
                foreach (
                    [
                        [$line - 1, $pos - 1], // top left
                        [$line - 1, $pos], // top
                        [$line - 1, $pos + 1], // top right
                        [$line, $pos - 1], // left
                        [$line, $pos + 1], // right
                        [$line + 1, $pos - 1], // bottom left
                        [$line + 1, $pos], // bottom
                        [$line + 1, $pos + 1], // bottom right
                    ]
                    as $adjacent
                ) {
                    if (isset($symbols[$adjacent[0]][$adjacent[1]])) {
                        $isAdjacent = true;
                        break;
                    }
                }
            }
        } else {
            if ($isAdjacent) {
                $sum += (int) $numberBuffer;
            }
            $isAdjacent = false;
            $numberBuffer = "";
        }
    }
    if ($isAdjacent) {
        $sum += (int) $numberBuffer;
    }
}
var_dump($sum);
