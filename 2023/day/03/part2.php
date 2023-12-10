<?php

$t = file_get_contents("input.txt");

$sum = 0;

$symbols = [];
$lines = explode("\n", $t);
foreach ($lines as $line => $text) {
    $matches = [];
    preg_match_all('~[#%&*+\-/=@$]~', $text, $matches, PREG_OFFSET_CAPTURE);
    foreach ($matches[0] as $match) {
        $pos = $match[1];
        $symbols[$line][$pos] = true;
        $numberSeenCount = 0;
        $numbersAt = [];
        foreach (
            [
                [
                    [$line - 1, $pos - 1], // top left
                    [$line - 1, $pos], // top
                    [$line - 1, $pos + 1], // top right
                ],
                [
                    [$line, $pos - 1], // left
                ],
                [
                    [$line, $pos + 1], // right
                ],
                [
                    [$line + 1, $pos - 1], // bottom left
                    [$line + 1, $pos], // bottom
                    [$line + 1, $pos + 1], // bottom right
                ],
            ]
            as $adjacent
        ) {
            $numberSeen = false;
            $lastIsNumber = false;
            foreach ($adjacent as $adjacentPos) {
                $isNumber = is_numeric(
                    $lines[$adjacentPos[0]][$adjacentPos[1]]
                );
                if ($isNumber && $lastIsNumber) {
                    continue;
                }
                if ($isNumber) {
                    $numbersAt[] = $adjacentPos;
                }
                $lastIsNumber = $isNumber;
            }
        }
        if (count($numbersAt) !== 2) {
            continue;
        }

        $numberBuffers = [];
        foreach ($numbersAt as [$lineNo, $posNo]) {
            while (true) {
                if (
                    $posNo - 1 >= 0 &&
                    is_numeric($lines[$lineNo][$posNo - 1])
                ) {
                    $posNo--;
                } else {
                    break;
                }
            }
            $numberBuffer = "";
            while (true) {
                $numberBuffer .= $lines[$lineNo][$posNo];
                if (
                    $posNo + 1 <= strlen($lines[$lineNo]) &&
                    is_numeric($lines[$lineNo][$posNo + 1])
                ) {
                    $posNo++;
                } else {
                    break;
                }
            }
            $numberBuffers[] = $numberBuffer;
        }

        $sum += (int) $numberBuffers[0] * (int) $numberBuffers[1];
    }
}
var_dump($sum);
