<?php
/**
 * POSITIVE repro for codevigilant.php.wordpress.ssrf.cleartext_http_remote.direct
 * Hardcoded cleartext HTTP endpoints passed to the WP HTTP API.
 */

$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
$country_request = wp_remote_get( 'http://ip-api.com/json/' . $ip . '?fields=country' );
$r = wp_remote_post( 'http://example.com/api', array( 'body' => $data ) );
$r2 = wp_remote_request( 'http://tracker.example.com/ping' );
