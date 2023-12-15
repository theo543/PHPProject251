<?php

function compile_view($path, $out) {
    $file = fopen($path, "r");
    while(($line = fgets($file)) !== false) {
        $trimmed = trim($line);
        $output .= $line . "\n";
    }
}
