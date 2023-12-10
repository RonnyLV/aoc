<?php

$t = file_get_contents("input.txt");

$sample = <<<TXT
Game 1: 3 blue, 4 red; 1 red, 2 green, 6 blue; 2 green
Game 2: 1 blue, 2 green; 3 green, 4 blue, 1 red; 1 green, 1 blue
Game 3: 8 green, 6 blue, 20 red; 5 blue, 4 red, 13 green; 5 green, 1 red
Game 4: 1 green, 3 red, 6 blue; 3 green, 6 red; 3 green, 15 blue, 14 red
Game 5: 6 red, 1 blue, 3 green; 2 blue, 1 red, 2 green
TXT;

$allowed = [
    "red" => 12,
    "green" => 13,
    "blue" => 14,
];

$sum = 0;

foreach (explode("\n", $t) as $line) {
    $matches = [];
    $e = explode(":", $line);
    $game = (int) explode(" ", $e[0])[1];
    $games = explode(";", $e[1]);
    $error = false;
    $colors = [
        "red" => 0,
        "green" => 0,
        "blue" => 0,
    ];
    foreach ($games as $thegame) {
        $thegame = trim($thegame);
        $sets = explode(",", $thegame);
        foreach ($sets as $set) {
            $explode = explode(" ", trim($set));
            $color = $explode[1];
            $count = (int) trim($explode[0]);
            if (!isset($allowed[$color])) {
                throw new \Exception("Color $color not allowed");
            }
            $colors[$color] = max($count, $colors[$color]);
        }
    }

    $thesum = $colors["red"] * $colors["green"] * $colors["blue"];
    $sum += $thesum;
}

echo $sum;
