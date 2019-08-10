<?php

function fixName($string) {

    $ar = str_split($string);

    $prev = null;

    foreach ($ar as &$char) {

        if (!is_null($prev)) {
            if ($prev == "_") {
                $char = strtoupper($char);
            }
        }

        $prev = $char;
    }

    return str_replace("_", "", implode("", $ar));
}