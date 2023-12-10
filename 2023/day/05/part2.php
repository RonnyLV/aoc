<?php

declare(strict_types=1);
const BATCH_SIZE = 150_000_000;

$startedAt = microtime(true);

$t = file_get_contents("input.txt");

$ts = <<<TXT
seeds: 79 14 55 13

seed-to-soil map:
50 98 2
52 50 48

soil-to-fertilizer map:
0 15 37
37 52 2
39 0 15

fertilizer-to-water map:
49 53 8
0 11 42
42 0 7
57 7 4

water-to-light map:
88 18 7
18 25 70

light-to-temperature map:
45 77 23
81 45 19
68 64 13

temperature-to-humidity map:
0 69 1
1 0 69

humidity-to-location map:
60 56 37
56 93 4
TXT;

$lines = explode("\n", $t);

$seeds = [];
$maps = [];
$currentMap = null;

foreach ($lines as $i => $line) {
    $line = trim($line);
    if ($i === 0) {
        $matches = [];
        $seedPairs = preg_split("/\s+/", substr($line, strlen("seeds: ")));
        for ($j = 0; $j < count($seedPairs); $j += 2) {
            $seeds[] = [(int) $seedPairs[$j], (int) $seedPairs[$j + 1]];
        }
        usort($seeds, fn($a, $b) => $a[0] - $b[0]);
        continue;
    }
    if (empty($line)) {
        $currentMap = null;
        continue;
    }
    if (str_ends_with($line, " map:")) {
        $currentMap = preg_split("/ /", $line)[0];
        continue;
    }
    if ($currentMap) {
        $maps[$currentMap][] = array_map("intval", preg_split("/\s+/", $line));
    }
}

$mapToMap = [
    "seed-to-soil" => null,
    "soil-to-fertilizer" => "seed-to-soil",
    "fertilizer-to-water" => "soil-to-fertilizer",
    "water-to-light" => "fertilizer-to-water",
    "light-to-temperature" => "water-to-light",
    "temperature-to-humidity" => "light-to-temperature",
    "humidity-to-location" => "temperature-to-humidity",
];
$sourceToDestMap = [];
foreach ($maps as $mapName => $map) {
    foreach ($map as $i => $row) {
        $times = $row[2] - 1;
        $sourceMap = $row[1] + $times;
        $destMap = $row[0] + $times;
        $sourceToDestMap[$mapName][] = [
            [$row[1], $sourceMap],
            [$row[0], $destMap],
        ];
    }
}

$mapToArray = [
    $sourceToDestMap["seed-to-soil"],
    $sourceToDestMap["soil-to-fertilizer"],
    $sourceToDestMap["fertilizer-to-water"],
    $sourceToDestMap["water-to-light"],
    $sourceToDestMap["light-to-temperature"],
    $sourceToDestMap["temperature-to-humidity"],
    $sourceToDestMap["humidity-to-location"],
];

$startRange = PHP_INT_MAX;
$endRange = 0;
foreach ($mapToArray as $index => $map) {
    foreach ($map as [$source, $dest]) {
        $sourceMin = $source[0];
        $sourceMax = $source[1];
        $startRange = min($startRange, $sourceMin);
        $endRange = max($endRange, $sourceMax);
    }
}

foreach ($seeds as $seed) {
    $startRange = min($startRange, $seed);
    $endRange = max($endRange, $seed);
}

function findTheDest(int $index, int $source): bool|int
{
    if (!isset($GLOBALS["mapToArray"][$index])) {
        return $source;
    }
    foreach ($GLOBALS["mapToArray"][$index] as $sourceToDest) {
        [$sourceRangeStart, $sourceRangeEnd] = $sourceToDest[0];
        if ($source >= $sourceRangeStart && $source <= $sourceRangeEnd) {
            [$destRangeStart, $destRangeEnd] = $sourceToDest[1];
            $offset = $source - $sourceRangeStart;
            return findTheDest($index + 1, $destRangeStart + $offset);
        }
    }

    return findTheDest($index + 1, $source);
}

$GLOBALS["mapToArray"] = &$mapToArray;
$GLOBALS["findTheDest"] = &$findTheDest;

$script = $argv[0];
if (isset($argv[1])) {
    $cmdRangeStart = (int) $argv[1];
}

$count = 0;
foreach ($seeds as $seedRangePairs) {
    $count += $seedRangePairs[1];
}

$scriptLines = "";

if (!isset($cmdRangeStart)) {
    foreach (range(0, (int) floor($count / BATCH_SIZE)) as $batchStart) {
        $scriptLines .= "php $script $batchStart\n";
    }
    file_put_contents("bruteforce.sh", $scriptLines);
    echo "Run script.sh using GNU parallel, e.g. `cat bruteforce.sh | parallel -j 8 --progress --eta`\n";
    echo "After script done, look for lowest number in seeds folder, e.g. `grep -h \"\" seeds/* | sort -n | uniq | head`\n";
    exit();
}

mkdir(__DIR__ . "/seeds/");

error_log("start: " . $cmdRangeStart . "\n");

$lastTime = microtime(true);
$minLocation = PHP_INT_MAX;
$currentCount = 0;
$processedCount = 0;
foreach ($seeds as $seedRangePairs) {
    $endSeed = $seedRangePairs[0] + $seedRangePairs[1];
    if ($currentCount < $seedRangePairs[1]) {
        $currentCount += $seedRangePairs[1];
        continue;
    }
    for ($seed = $seedRangePairs[0]; $seed <= $endSeed; $seed++) {
        $currentCount++;
        if ($currentCount < $cmdRangeStart * BATCH_SIZE) {
            continue;
        }
        $processedCount++;
        if ($location = \findTheDest(0, $seed)) {
            $newMin = \min($location, $minLocation);
            if ($newMin !== $minLocation) {
                file_put_contents(
                    __DIR__ . "/seeds/" . mt_rand(),
                    $newMin . "\n"
                );
            }
            $minLocation = $newMin;
        }
        if ($processedCount > BATCH_SIZE) {
            break;
        }
    }
}

echo $minLocation . "\n";
echo "time took: " . (microtime(true) - $startedAt) . "\n";
file_put_contents(__DIR__ . "/seeds/" . mt_rand(), $minLocation . "\n");
