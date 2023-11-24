<?php

class View {
    private $view_name;
    private $view_data;

    public function __construct(string $view_name) {
        $this->view_name = $view_name;
        $this->view_data = array("eh" => function (string $str): string {
            return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5);
        });
    }

    public function set(string $key, $value): void {
        $this->view_data[$key] = $value;
    }

    public function set_many(array $data): void {
        foreach($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function render(): void {
        extract($this->view_data);
        $view_data = $this->view_data;
        require "views/" . $this->view_name . ".view.php";
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
