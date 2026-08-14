<?php
// NEGATIVE: decompressed size is bounded before use - rule must NOT fire
function handle_upload( $file ) {
    $dirty = file_get_contents( $file );
    if ( 0 === mb_strpos( $dirty, "\x1f\x8b\x08" ) ) {
        $dirty = gzdecode( $dirty );
        if ( false === $dirty ) {
            return false;
        }
        if ( strlen( $dirty ) > 10 * 1024 * 1024 ) {
            return false;
        }
    }
    return $dirty;
}
