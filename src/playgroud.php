<?php

function transformWords($w1, $w2) {
    $len1 = strlen($w1);
    $len2 = strlen($w2);

    if ($len1 > $len2) {
        $w1 = substr($w1, 0, $len2);
    } elseif ($len2 > $len1) {
        $w2 = substr($w2, 0, $len1);
    }

    $result = $w1[$len1 - 1] . substr($w1, 0, $len1 - 1);
    $result .= $w2[$len2 - 1] . substr($w2, 0, $len2 - 1);

    return $result;
}

$w1 = "BANANA";
$w2 = "APPLE";
$result = transformWords($w1, $w2);
echo $result;
