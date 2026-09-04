<?php
/**
 * NEGATIVE repro for codevigilant.php.wordpress.ssrf.cleartext_http_remote.direct
 * HTTPS endpoints or user-validated URLs only.
 */

$r  = wp_remote_get( 'https://api.example.com/json' );
$r2 = wp_remote_post( esc_url( 'https://send.example.com/process-plugin-data' ), $args );
$r3 = wp_remote_get( esc_url_raw( $user_url ) );
