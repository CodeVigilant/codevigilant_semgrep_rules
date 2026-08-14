<?php
// POSITIVE: naive prefix containment check - sibling dir "wp-content-evil" also matches base "wp-content"
$base = untrailingslashit(WP_CONTENT_DIR);
if (substr($path, 0, strlen($base)) === $base) {
    file_put_contents($path, $data); // write "allowed" outside the jail
}
