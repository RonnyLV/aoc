<?php

$t = file_get_contents("input.txt");

$sample = <<<TXT
Card 1: 41 48 83 86 17 | 83 86  6 31 17  9 48 53
Card 2: 13 32 20 16 61 | 61 30 68 82 17 32 24 19
Card 3:  1 21 53 59 44 | 69 82 63 72 16 21 14  1
Card 4: 41 92 73 84 69 | 59 84 76 51 58  5 54 83
Card 5: 87 83 26 28 32 | 88 30 70 12 93 22 82 36
Card 6: 31 18 13 56 72 | 74 77 10 23 35 67 36 11
TXT;

$sum = 0;
$lines = explode("\n", $t);

$cards = [];
$cardCopies = [];
$winsPerCard = [];
foreach ($lines as $lineNo => $line) {
    $cardCopies[$lineNo] = 1;
    $winsPerCard[$lineNo] = 0;
}
foreach ($lines as $lineNo => $line) {
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

    $times = 0;
    foreach ($winningCards as $winningCard => $value) {
        if (isset($myCards[$winningCard])) {
            $times++;
        }
    }
    $winsPerCard[$lineNo] = $times;
}

foreach ($winsPerCard as $cardId => $wins) {
    if ($wins === 0) {
        continue;
    }
    foreach (range($cardId + 1, $cardId + $wins) as $nextCard) {
        if (!isset($cardCopies[$nextCard])) {
            break;
        }
        $cardCopies[$nextCard] += $cardCopies[$cardId];
    }
}

var_dump(
    array_reduce(
        $cardCopies,
        function ($item, $acc) {
            return $acc + $item;
        },
        0
    )
);
