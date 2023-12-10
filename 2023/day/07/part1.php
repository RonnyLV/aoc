<?php

$t = file_get_contents("input.txt");

$ts = <<<TXT
32T3K 765
T55J5 684
KK677 28
KTJJT 220
QQQJA 483
TXT;

$st = <<<TXT
AAAAA 765
AA8AA 684
23332 28
TTT98 28
23432 28
A23A4 28
23456 28
TXT;

$cardMap = [
    "A",
    "K",
    "Q",
    "J",
    "T",
    "9",
    "8",
    "7",
    "6",
    "5",
    "4",
    "3",
    "2",
    "1",
];

$cardPower = array_flip(array_reverse($cardMap));

$hands = [];

$lines = explode("\n", $t);
foreach ($lines as $line) {
    $strs = preg_split("/\s+/", $line);
    $cards = str_split($strs[0]);
    $cardsTotal = 0;
    $pairs = [];
    $cardsNat = "";
    foreach ($cards as $i => $card) {
        $f = $cardPower[$card] * pow(10, 5 - $i);
        $cardsTotal += $f;
        $pairs[$card] = ($pairs[$card] ?? 1) + 1;
        $cardsNat .= chr(65 + $cardPower[$card]);
    }
    $pairTotal = 1;
    foreach ($pairs as $pair) {
        if ($pair === 1) {
            continue;
        }
        $pairTotal += pow(10, $pair);
    }
    $hands[] = [
        "cards" => $strs[0],
        "bid" => (int) $strs[1],
        "cardPower" => $cardsTotal,
        "pairTotal" => $pairTotal,
        "cardsNat" => $cardsNat,
    ];
}

usort($hands, static function ($a, $b) {
    $result = $a["pairTotal"] <=> $b["pairTotal"];
    if ($result === 0) {
        return $a["cardsNat"] <=> $b["cardsNat"];
    }
    return $result;
});

//var_dump($hands);

$result = 0;

foreach ($hands as $rank => $hand) {
    $result += $hand["bid"] * ($rank + 1);
}

var_dump($result);
//251667396
//253200022
//250668733
//253205868
