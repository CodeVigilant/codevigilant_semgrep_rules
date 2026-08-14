<?php
// POSITIVE: Sanitizer without removeRemoteReferences - rule must fire
function sanitize_upload( $file ) {
    $sanitizer = new \enshrined\svgSanitize\Sanitizer();
    $sanitizer->minify( true );
    $clean = $sanitizer->sanitize( $file );
    return $clean;
}
