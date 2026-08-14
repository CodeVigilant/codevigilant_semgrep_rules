<?php
// NEGATIVE: containment check with trailing separator - correct boundary
$base = trailingslashit(WP_CONTENT_DIR);
if (str_starts_with($path, $base)) {
    file_put_contents($path, $data);
}
