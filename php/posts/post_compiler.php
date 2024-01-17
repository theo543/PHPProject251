<?php
declare(strict_types=1);

class PostCompileException extends RuntimeException {
    public function __construct(string $message) {
        parent::__construct($message);
    }
}

function compile_post(string $content): string {
    $linenum = 0;
    $buffer = "";
    foreach (explode("\n", $content) as $line) {
        $buffer .= "Line $linenum: $line // TODO implement some actual Markdown features";
        $linenum++;
    }
    return $buffer;
}
