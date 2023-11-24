<?php

class View {
    private $view_name;
    private $view_data;

    public function __construct(string $view_name) {
        $this->view_name = $view_name;
        $this->view_data = [];
    }

    public function set(string $key, $value): void {
        $this->view_data[$key] = $value;
    }

    public function set_many(array $data): void {
        foreach($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    private function eh(string $str): string {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5);
    }

    public function render(): void {
        extract($this->view_data);
        require "views/" . $this->view_name . ".view.php";
    }
}

function create_view_callback(string $view_name): callable {
    return function() use ($view_name) {
        $view = new View($view_name);
        $view->render();
    };
}
