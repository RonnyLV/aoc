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
JJJJJ 1000000
AAJJ2 5
JJAA2 5
TXT;

$ts = <<<TXT
AAAAA 5
AAAJJ 5
AAJJ2 5
JJJJ4 5
JJAA2 5
AAAA2 5
AAAJ2 5
AAAJJ 5
KJAJJ 1
TXT;

$cardMap = ["A", "K", "Q", "T", "9", "8", "7", "6", "5", "4", "3", "2", "J"];

$cardPower = array_flip(array_reverse($cardMap));

$hands = [];

$lines = explode("\n", $t);
foreach ($lines as $line) {
    $strs = preg_split("/\s+/", $line);
    $cards = str_split($strs[0]);
    $cardsTotal = 0;
    $pairs = [];
    $cardsNat = "";
    $jokers = 0;
    foreach ($cards as $i => $card) {
        if ($card === "J") {
            $jokers++;
        }
        $f = $cardPower[$card] * pow(10, 5 - $i);
        $cardsTotal += $f;
        $cardsNat .= chr(65 + $cardPower[$card]);
        $pairs[$card] = [
            "card" => $card,
            "cardNat" => $cardsNat,
            "pairs" => ($pairs[$card]["pairs"] ?? 0) + 1,
        ];
    }
    $pairTotal = 1;
    usort($pairs, function ($a, $b) {
        $res = $b["pairs"] <=> $a["pairs"];
        if ($res === 0) {
            return $b["cardNat"] <=> $a["cardNat"];
        }
        return $res;
    });
    $jokersUsed = false;
    foreach ($pairs as $i => $pair) {
        if ($pair["card"] === "J") {
            unset($pairs[$i]);
        }
    }
    foreach ($pairs as $pair) {
        if ($pair === 1) {
            continue;
        }
        $pairTotal += pow(10, $pair["pairs"] + $jokers);
        $jokers = 0;
    }
    if ($jokers > 0) {
        $pairTotal += pow(10, $jokers);
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

$cardOrder = [];

foreach ($hands as $rank => $hand) {
    $result += $hand["bid"] * ($rank + 1);
    $cardOrder[] = $hand["cards"] . " " . $hand["pairTotal"];
}
var_dump($cardOrder);
var_dump($result);
//251667396
//253200022
//250668733
//253205868
//254197162
//253428952
//253710308
//253907829
