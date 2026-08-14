<?php
// NEGATIVE: class name derived from sanitized / allowlisted input
$route = sanitize_key($_REQUEST['route']);
$allowed = ['home', 'about', 'contact'];
if (in_array($route, $allowed, true)) {
    $class = 'App_' . ucfirst($route) . 'Controller';
    if (class_exists($class)) {
        $obj = new $class();
    }
}
