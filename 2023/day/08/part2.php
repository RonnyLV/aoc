<?php

$t = file_get_contents("input.txt");

$ts = <<<TXT
LR

11A = (11B, XXX)
11B = (XXX, 11Z)
11Z = (11B, XXX)
22A = (22B, XXX)
22B = (22C, 22C)
22C = (22Z, 22Z)
22Z = (22B, 22B)
XXX = (22Z, 22Z)
TXT;

$lines = explode("\n", $t);

$movePattern = str_split(array_shift($lines));
$movePatternSize = count($movePattern);

array_shift($lines);

$moves = [];

$nodesEndWithA = [];
$nodesEndWithZ = [];

foreach ($lines as $line) {
    $matches = [];
    preg_match(
        "/([A-Z0-9]+)\s*=\s*\(([A-Z0-9]+),\s*([A-Z0-9]+)\)/",
        $line,
        $matches
    );
    $moves[$matches[1]] = ["L" => $matches[2], "R" => $matches[3]];
    if (str_ends_with($matches[1], "A")) {
        $nodesEndWithA[] = [
            "node" => $matches[1],
            "lastNode" => $matches[1],
            "stepsTaken" => 0,
        ];
    }
    if (Str_ends_with($matches[1], "Z")) {
        $nodesEndWithZ[$matches[1]] = true;
    }
}

$stepsTaken = [];
$numbers = 1;

foreach (array_keys($moves) as $nodeName) {
    $currentStep = $nodeName;
    $moveIndex = 0;
    while (!(isset($nodesEndWithZ[$currentStep]) && $moveIndex > 0)) {
        $i = $moveIndex % $movePatternSize;
        $leftOrRight = $movePattern[$i];
        $currentStep = $moves[$currentStep][$leftOrRight];
        $moveIndex++;
    }
    $stepsTaken[$nodeName] = [
        "stepsToZ" => $moveIndex,
        "stepsTaken" => $moveIndex,
        "endsWith" => $currentStep,
    ];
    foreach ($nodesEndWithA as &$nodeEndWithA) {
        if ($nodeEndWithA["node"] === $nodeName) {
            $nodeEndWithA["stepsTaken"] = $moveIndex;
            $nodeEndWithA["lastNode"] = $currentStep;
            break;
        }
    }
}

$timeStart = microtime(true);
usort($nodesEndWithA, function ($a, $b) {
    return $b["stepsTaken"] <=> $a["stepsTaken"];
});
$maxNumber = $nodesEndWithA[0]["stepsTaken"];
for ($i = 0; $i < count($nodesEndWithA); $i++) {
    $targetNode = &$nodesEndWithA[$i];
    if ($targetNode["stepsTaken"] < $maxNumber) {
        while ($targetNode["stepsTaken"] < $maxNumber) {
            $lastNode = $stepsTaken[$targetNode["lastNode"]];
            $targetNode["stepsTaken"] += $lastNode["stepsToZ"];
            $targetNode["lastNode"] = $lastNode["endsWith"];
        }
        usort($nodesEndWithA, function ($a, $b) {
            return $b["stepsTaken"] <=> $a["stepsTaken"];
        });
        $maxNumber = $nodesEndWithA[0]["stepsTaken"];
        $i = 0;
    }

    if ($timeStart + 10 < microtime(true)) {
        var_dump($nodesEndWithA);
        $timeStart = microtime(true);
    }
}

var_dump($nodesEndWithA);
