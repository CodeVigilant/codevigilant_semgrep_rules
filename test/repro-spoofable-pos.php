<?php
// POSITIVE repro: spoofable identity gates.

// 1) User-Agent used to decide whether to show the normal site (CWE-290)
function load_page() {
    $show_maintenance_page = 1;
    if ($this->check_referrer()) {
        $show_maintenance_page = 0;
    }
    return $show_maintenance_page;
}

function check_referrer() {
    $crawlers = array('Google' => 'Googlebot', 'Bing' => 'bingbot');
    foreach ($crawlers as $name => $ua) {
        if (stripos($_SERVER['HTTP_USER_AGENT'], $ua) !== false) {
            return true;
        }
    }
    return false;
}

// 2) cookie compared against plain md5 of stored password (CWE-916)
function is_unlocked() {
    $opts = get_option('my_opts');
    if (isset($_COOKIE['site_unlocked']) && $_COOKIE['site_unlocked'] == md5($opts['site_password'])) {
        return true;
    }
    return false;
}
