<?php

$t = file_get_contents("input.txt");

$ts = <<<TXT
LLR

AAA = (BBB, BBB)
BBB = (AAA, ZZZ)
ZZZ = (ZZZ, ZZZ)
TXT;

$lines = explode("\n", $t);

$movePattern = str_split(array_shift($lines));
$movePatternSize = count($movePattern);

array_shift($lines);

$moves = [];

foreach ($lines as $line) {
    $matches = [];
    preg_match("/([A-Z]+)\s*=\s*\(([A-Z]+),\s*([A-Z]+)\)/", $line, $matches);
    $moves[$matches[1]] = ["L" => $matches[2], "R" => $matches[3]];
}

$currentStep = "AAA";
$moveIndex = 0;
$movePatternSize = count($movePattern);
while ($currentStep !== "ZZZ") {
    $i = $moveIndex % $movePatternSize;
    $leftOrRight = $movePattern[$i];
    $currentStep = $moves[$currentStep][$leftOrRight];
    $moveIndex++;
}

var_dump($moveIndex);
