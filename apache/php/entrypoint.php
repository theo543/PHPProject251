<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

require_once("Router.php");
require_once("test_endpoints.php");

$r = new Router;
register_test_endpoints($r);
$r->run();
