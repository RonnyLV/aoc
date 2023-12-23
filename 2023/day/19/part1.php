<?php

$t = <<<'TXT'
px{a<2006:qkq,m>2090:A,rfg}
pv{a>1716:R,A}
lnx{m>1548:A,A}
rfg{s<537:gd,x>2440:R,A}
qs{s>3448:A,lnx}
qkq{x<1416:A,crn}
crn{x>2662:A,R}
in{s<1351:px,qqz}
qqz{s>2770:qs,m<1801:hdj,R}
gd{a>3333:R,R}
hdj{m>838:A,pv}

{x=787,m=2655,a=1222,s=2876}
{x=1679,m=44,a=2067,s=496}
{x=2036,m=264,a=79,s=2244}
{x=2461,m=1339,a=466,s=291}
{x=2127,m=1623,a=2188,s=1013}
TXT;

$t = file_get_contents("input.txt");

$exploded = explode("\n\n", $t);
$rulesLines = $exploded[0];
$partsLines = $exploded[1];

var_dump($rulesLines, $partsLines);

$rules = [];

foreach (explode("\n", $rulesLines) as $ruleLine) {
    $matches = [];
    preg_match("/(.*?)\{(.*?)}/", $ruleLine, $matches);
    $ruleName = $matches[1];
    $ruleFlow = [];
    foreach (explode(",", $matches[2]) as $ruleFlowLine) {
        $matches = [];
        preg_match("/(.*)([<>])(.*):(.*)/", $ruleFlowLine, $matches);
        if (!$matches) {
            $ruleFlow[] = [
                "operation" => "goto",
                "nextRule" => $ruleFlowLine,
            ];
        } else {
            $ruleFlow[] = [
                "subject" => $matches[1],
                "operation" => $matches[2],
                "comparisonValue" => $matches[3],
                "nextRule" => $matches[4],
            ];
        }
    }
    $rules[$ruleName] = $ruleFlow;
}

$acceptedParts = [];
foreach (explode("\n", $partsLines) as $partLine) {
    $matches = [];
    preg_match_all("/[xmas]=([0-9]+)/", $partLine, $matches);
    $partParts = [
        "x" => $matches[1][0],
        "m" => $matches[1][1],
        "a" => $matches[1][2],
        "s" => $matches[1][3],
    ];
    $accepted = false;
    $ruleName = "in";
    while (true) {
        if ($ruleName === "A") {
            $accepted = true;
            break;
        }
        if ($ruleName === "R") {
            break;
        }
        foreach ($rules[$ruleName] as $ruleFlow) {
            if ($ruleFlow["operation"] === "goto") {
                $ruleName = $ruleFlow["nextRule"];
                continue 2;
            }
            if ($ruleFlow["operation"] === "<") {
                if (
                    $partParts[$ruleFlow["subject"]] <
                    $ruleFlow["comparisonValue"]
                ) {
                    $ruleName = $ruleFlow["nextRule"];
                    continue 2;
                }
            } elseif ($ruleFlow["operation"] === ">") {
                if (
                    $partParts[$ruleFlow["subject"]] >
                    $ruleFlow["comparisonValue"]
                ) {
                    $ruleName = $ruleFlow["nextRule"];
                    continue 2;
                }
            }
        }
        echo "something went wrong";
        exit();
    }
    if ($accepted) {
        $acceptedParts[] = $partParts;
    }
}

$sum = 0;
foreach ($acceptedParts as $acceptedPart) {
    $sum += array_sum($acceptedPart);
}
echo $sum;
