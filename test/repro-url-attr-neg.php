<?php
// NEGATIVE: src attribute read only inside a guarded removal - rule must NOT fire
function scrub( $element ) {
    if ( ! preg_match( '~^(https?|data:image|/|#)~i', $element->getAttribute( 'src' ) ) ) {
        $element->removeAttribute( 'src' );
    }
}
