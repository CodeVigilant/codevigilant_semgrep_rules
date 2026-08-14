<?php
// POSITIVE: file contents decompressed without any size bound - rule must fire
function handle_upload( $file ) {
    $dirty = file_get_contents( $file );
    if ( 0 === mb_strpos( $dirty, "\x1f\x8b\x08" ) ) {
        $dirty = gzdecode( $dirty );
        if ( false === $dirty ) {
            return false;
        }
    }
    return $dirty;
}
