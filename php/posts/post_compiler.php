<?php
declare(strict_types=1);

function compile_post(string $content): string {
    $linenum = 0;
    $buffer = "";
    $list_expr = "/^\\*( *)(.*)$/";
    $list_indent = 0;
    $elem_end = null;
    $end_list = function() use (&$list_indent, &$buffer) {
        if($list_indent > 0) {
            $buffer .= "</ul>";
            $list_indent = 0;
        }
    };
    $end_elem = function() use (&$elem_end, &$buffer) {
        if($elem_end !== null) {
            $buffer .= $elem_end;
            $elem_end = null;
        }
    };
    function esc(string $str): string {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5);
    }
    function headings(string $line): string {
        $h1_expr = "/^# (.*)$/";
        $h2_expr = "/^## (.*)$/";
        $h3_expr = "/^### (.*)$/";
        if(preg_match($h1_expr, $line, $matches)) {
            return "<h1>" . esc($matches[1]) . "</h1>";
        } else if(preg_match($h2_expr, $line, $matches)) {
            return "<h2>" . esc($matches[1]) . "</h2>";
        } else if(preg_match($h3_expr, $line, $matches)) {
            return "<h3>" . esc($matches[1]) . "</h3>";
        }
        return esc($line);
    }
    foreach (explode("\n", $content) as $line) {
        if(str_ends_with($line, "\r")) {
            $line = substr($line, 0, -1);
        }
        if(str_starts_with($line, '#')) {
            $end_list();
            $end_elem();
            $buffer .= headings($line);
        } else if($elem_end !== null && !str_starts_with($line, '*') && $line !== '') {
            $buffer .= esc($line);
        } else if(preg_match($list_expr, $line, $matches)) {
            $end_elem();
            if($list_indent == 0) {
                $buffer .= "<ul>";
                $list_indent = strlen($matches[1]);
            }
            $buffer .= "<li>" . esc($matches[2]);
            $elem_end = "</li>";
        } else {
            $end_elem();
            $end_list();
            if($line !== '') {
                $buffer .= "<p>" . esc($line);
                $elem_end = "</p>";
            }
        }
        $linenum++;
    }
    return $buffer;
}
