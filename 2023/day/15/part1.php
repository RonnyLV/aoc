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

foreach (explode(",", $t) as $item) {
    $currentValue = reindeerHash($item);
    $sum += $currentValue;
}
echo $sum;
