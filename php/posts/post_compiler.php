<?php
declare(strict_types=1);

function compile_post(string $content): string {
    $linenum = 0;
    $buffer = "";
    foreach (explode("\n", $content) as $line) {
        $buffer .= "Line $linenum: $line // TODO implement some actual Markdown features";
        $linenum++;
    }
    return $buffer;
}
