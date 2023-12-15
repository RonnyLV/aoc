<?php

function reindeerHash(string $item): int
{
    $currentValue = 0;
    foreach (str_split($item) as $char) {
        $currentValue += ord($char);
        $currentValue *= 17;
        $currentValue = $currentValue % 256;
    }
    return $currentValue;
}

$t = "rn=1,cm-,qp=3,cm=2,qp-,pc=4,ot=9,ab=5,pc-,pc=6,ot=7";
$t = file_get_contents("input.txt");

$sum = 0;

$ops = [];
$boxes = array_fill(0, 255, []);

foreach (explode(",", $t) as $item) {
    $label = preg_split("/[-=]/", $item)[0];
    $box = reindeerHash($label);

    if (str_ends_with($item, "-")) {
        $op = "-";
        if (isset($boxes[$box][$label])) {
            unset($boxes[$box][$label]);
        }
    } else {
        $focal = preg_split("/=/", $item)[1];
        $op = "=";
        $boxes[$box][$label] = [
            "label" => $label,
            "op" => $op,
            "focal" => (int) $focal,
            "box" => $box,
        ];
    }
    $test = 1;
}

$sum = 0;
foreach ($boxes as $boxIndex => $box) {
    foreach (array_values($box) as $slotIndex => $label) {
        $sum += ($boxIndex + 1) * ($slotIndex + 1) * $label["focal"];
    }
}

var_dump($sum);
