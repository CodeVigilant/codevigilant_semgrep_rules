<?php
// POSITIVE repro: client-controlled state trusted in conditional logic.

// 1) cookie-based "unlock/bypass" flag -> CWE-565 / CWE-287
function show_site() {
    $show_maintenance_page = 1;
    if (!empty($_COOKIE['skip_maintenance_mode'])) {
        $show_maintenance_page = 0;
    }
    return $show_maintenance_page;
}

// 2) negated isset() on GET param used to skip a protection gate -> CWE-425
function load_page() {
    $locked = true;
    if ($locked && !isset($_GET['mainwpsignature'])) {
        $locked = false;
    }
    return $locked;
}

// 3) cookie equality check
function is_unlocked() {
    if ($_COOKIE['site_unlocked'] == md5('secret')) {
        return true;
    }
    return false;
}
