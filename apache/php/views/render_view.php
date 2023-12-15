<?php

require_once "auth/recaptcha.php";
require_once "views/render_compiler.php";

class View {
    private $view_name;
    private $view_data;

    public function __construct(string $view_name) {
        $this->view_name = $view_name;
        $this->view_data = array();
    }

    public function set(string $key, $value): View {
        $this->view_data[$key] = $value;
        return $this;
    }

    public function set_many(array $data): View {
        foreach($data as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    public function render(): void {
        $path = "views/" . $this->view_name . ".view.php";
        $os_tmpdir = sys_get_temp_dir();
        if($os_tmpdir[strlen($os_tmpdir) - 1] === "/") {
            $os_tmpdir = substr($os_tmpdir, 0, strlen($os_tmpdir) - 1);
        }
        $compiled = $os_tmpdir . "/views_compiled/" . $this->view_name . ".view.php";
        ensure_compiled($path, $compiled);
        extract($this->view_data);
        require $compiled;
    }

    public function callback() {
        return function() {
            $this->render();
        };    
    }
}

function view_with_account(string $view_name, Account $account): View {
    $view = new View($view_name);
    $view->set("account", $account);
    return $view;
}

function view(string $view_name): View {
    return new View($view_name);
}
