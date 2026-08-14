<?php
// NEGATIVE: Sanitizer WITH removeRemoteReferences(true) - rule must NOT fire
function sanitize_upload( $file ) {
    $sanitizer = new \enshrined\svgSanitize\Sanitizer();
    $sanitizer->removeRemoteReferences( true );
    $sanitizer->minify( true );
    $clean = $sanitizer->sanitize( $file );
    return $clean;
}
