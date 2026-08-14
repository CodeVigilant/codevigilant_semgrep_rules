<?php
// POSITIVE: src attribute read and used with no scheme validation - rule must fire
function scrub( $element ) {
    $src = $element->getAttribute( 'src' );
    return $src;
}
