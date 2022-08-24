<?php
function convertString($a, $b)
{
    $substringСheck = (mb_substr_count($a, $b));
    if ($substringСheck >= 2) {
        $pos = stripos($a, $b, 2);
        $b2 = strrev($b);
        $b2Length = mb_strlen($b2);
        $pos1 = substr_replace($a, $b2, $pos, $b2Length);
        return $pos1;
    } else {
        return $a;
    }
}
// $a = "string substring string substring string substring";
// $b = "substring";
//print_r(convertString($a, $b));
