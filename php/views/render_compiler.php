<?php
declare(strict_types=1);

class ViewCompileException extends RuntimeException {
    public function __construct(string $message) {
        parent::__construct($message);
    }
}

function remove_esc(string $str): string {
    $escape_expr = "{^\s*ESC='(.*)'$}u";
    if(preg_match($escape_expr, $str, $matches)) {
        return $matches[1];
    } else {
        return $str;
    }
}

function compile_view(string $path, string $mixin_nested_view = "", string|null $parent_view_path = null): string {
    if(!preg_match("/\.view\.php$/", $path)) {
        if($parent_view_path !== null) {
            // imports are relative to the parent view
            $path = dirname($parent_view_path) . "/" . $path . ".view.php";
        } else {
            throw new ViewCompileException("Attempted to import extensionless file: '$path' despite not having a parent view path for relative imports.");
        }
    }
    $mixin_expr = "{^\s*<MIXIN (.*)/>$}u";
    $begin_nest_expr = "{^\s*<MIXIN_NEST (.*)>$}u";
    $end_nest_expr = "{^\s*</MIXIN_NEST>$}u";
    $mixin_point_expr = "{^\s*<MIXIN_POINT/>$}u";
    $interpolate_expr = "{\{\{\{(?:([\w!]*?)\|)?(.*?)\}\}\}}u";
    $if_expr = "{^\s*<IF (.*)>$}u";
    $else_expr = "{^\s*<ELSE/>$}u";
    $endif_expr = "{^\s*</IF>$}u";
    $for_expr = "{^\s*<FOR (.*)>$}u";
    $endfor_expr = "{^\s*</FOR>$}u";
    $buffer_stack = [""];
    $mixin_stack = [];
    $append = function($str) use(&$buffer_stack) {
        $buffer_stack[count($buffer_stack) - 1] .= $str;
    };
    $file = fopen($path, "r");
    if(!$file) {
        throw new ViewCompileException("Could not open file: $path");
    }
    $linenum = 0;
    while($line = fgets($file)) {
        $linenum++;
        if(preg_match($if_expr, $line, $matches)) {
            $cond = remove_esc($matches[1]);
            $append("<?php if($cond): ?>");
        } else if(preg_match($else_expr, $line)) {
            $append("<?php else: ?>");
        } else if(preg_match($endif_expr, $line)) {
            $append("<?php endif; ?>");
        } else if(preg_match($begin_nest_expr, $line, $matches)) {
            array_push($buffer_stack, "");
            array_push($mixin_stack, remove_esc($matches[1]));
        } else if(preg_match($end_nest_expr, $line)) {
            if(count($buffer_stack) === 1) {
                throw new ViewCompileException("Unmatched ###END_NEST### at line $linenum");
            }
            $nested_view_name = null;
            try {
                $mixin_content = array_pop($buffer_stack);
                $nested_view_name = array_pop($mixin_stack);
                $append(compile_view($nested_view_name, $mixin_content, $path));
            } catch(ViewCompileException $e) {
                throw new ViewCompileException("Error compiling nested view '$nested_view_name' closed at line $linenum: " . $e->getMessage());
            }
        } else if(preg_match($mixin_expr, $line, $matches)) {
            try {
                $imported_view_name = remove_esc($matches[1]);
                $append(compile_view($imported_view_name, "", $path));
            } catch(ViewCompileException $e) {
                throw new ViewCompileException("Error compiling imported view '$imported_view_name' at line $linenum: " . $e->getMessage());
            }
        } else if(preg_match($mixin_point_expr, $line)) {
            $append($mixin_nested_view);
        } else if(preg_match($for_expr, $line, $matches)) {
            $iter = remove_esc($matches[1]);
            $append("<?php foreach($iter)" . ": ?>");
        } else if(preg_match($endfor_expr, $line)) {
            $append("<?php endforeach; ?>");
        } else {
            $line = preg_replace_callback($interpolate_expr, function(array $matches) use ($linenum) {
                $interp_flag = $matches[1];
                if($interp_flag === "") {
                    $interp_flag = 'h';
                }
                $interp_content = $matches[2];
                switch($interp_flag) {
                    case "h": // html escape
                        return "<?= htmlspecialchars($interp_content, ENT_QUOTES | ENT_HTML5) ?>";
                    case "!" : // no escape
                        return "<?= $interp_content ?>";
                    default:
                        throw new ViewCompileException("Invalid interpolation flag '$interp_flag' in interpolation $matches[0] at line $linenum");
                }
            }, $line);
            $append($line);
        }
    }
    if(count($buffer_stack) !== 1) {
        throw new ViewCompileException("Unclosed ###MIXIN_NEST(...)###");
    }
    fclose($file);
    return $buffer_stack[0];
}

function ensure_compiled($path, $compiled_path): void {
    $compiled_dir = sys_get_temp_dir();
    $compiled_dir = preg_replace("/\/$/", "", $compiled_dir); // remove trailing slash if present
    $compiled_dir .= "/views_compiled";
    if(!file_exists($compiled_dir)) { // file_exists actually works for directories too
        $success = mkdir($compiled_dir, 0700);
        if($success === false) {
            throw new ViewCompileException("Could not create directory: $compiled_dir");
        }
    }
    if(!file_exists($compiled_path) || filemtime($path) > filemtime($compiled_path)) { //TODO does not handle dependencies
        $compiled = compile_view($path);
        $success = file_put_contents($compiled_path, $compiled);
        if($success === false) {
            throw new ViewCompileException("Could not write to file: $compiled_path");
        } else if($success !== strlen($compiled)) {
            throw new ViewCompileException("Could not write all bytes to file: $compiled_path, wrote $success bytes, expected " . strlen($compiled));
        }
    }
}
