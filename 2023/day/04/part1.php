<?php

$t = file_get_contents("input.txt");

$sum = 0;
$lines = explode("\n", $t);

$cards = [];
foreach ($lines as $line) {
    $cardParts = explode(":", $line);
    $cardNumberParts = explode("|", $cardParts[1]);
    $winningCardsStr = trim($cardNumberParts[0]);
    $myCardsStr = trim($cardNumberParts[1]);

    $winningCards = [];
    $myCards = [];

    $cardId = preg_split("/\s+/", $cardParts[0])[1];

    foreach (preg_split("/\s+/", $winningCardsStr) as $winningCard) {
        $winningCards[$winningCard] = true;
    }

    foreach (preg_split("/\s+/", $myCardsStr) as $myCard) {
        $myCards[$myCard] = true;
    }

    $pow = -1;
    foreach ($winningCards as $winningCard => $value) {
        if (isset($myCards[$winningCard])) {
            $pow++;
        }
    }

    if ($pow >= 0) {
        $sum += pow(2, $pow);
    }
}

var_dump($sum);
