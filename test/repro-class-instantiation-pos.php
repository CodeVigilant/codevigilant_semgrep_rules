<?php
// POSITIVE: class name built from request input, no validation (LFI via autoloader)
$route = $_REQUEST['route'];
$class = 'App_' . implode('_', explode('-', $route)) . 'Controller';
if (class_exists($class)) {
    $obj = new $class();
}
