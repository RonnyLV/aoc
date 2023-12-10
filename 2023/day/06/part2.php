<?php

$t = file_get_contents("input.txt");

$lines = explode("\n", $t);

$timesStr = preg_split("/\s+/", $lines[0]);
$distancesStr = preg_split("/\s+/", $lines[1]);

$records = [];
foreach ($timesStr as $i => $time) {
    if ($i === 0) {
        continue;
    }
    $records[] = [
        "time" => (int) $time,
        "distance" => (int) $distancesStr[$i],
    ];
}

function getTimes(int $recordTime, int $recordDistance)
{
    $ways = 0;
    $mms = 1;
    while ($mms++) {
        $waitTime = $mms;
        $speedingTime = $recordTime - $waitTime;
        if ($speedingTime === 0) {
            break;
        }
        $maxDistance = $speedingTime * $mms;
        if ($maxDistance > $recordDistance) {
            $ways++;
        }
    }
    return $ways;
}

$result = 1;
foreach ($records as $record) {
    $result *= getTimes($record["time"], $record["distance"]);
}
var_dump($result);
