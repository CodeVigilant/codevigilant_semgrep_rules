<?php
// NEGATIVE: loop over a fixed server-side list, not request-derived
function fixed_list_handler() {
    $known_ids = [ 1, 2, 3 ];
    foreach ( $known_ids as $id ) {
        update_post_meta( $id, 'key', 'value' );
    }
}

// NEGATIVE: request-derived value but no loop (single lookup)
function single_lookup( WP_REST_Request $request ) {
    $id = (int) $request->get_param( 'image_id' );
    return get_post_meta( $id, 'key', true );
}

// NEGATIVE: loop over request-derived array but NO database work inside
// (pure computation / sanitization only)
function no_db_work( WP_REST_Request $request ) {
    $body = json_decode( $request->get_body() );
    $ids = $body->ids ?? [];

    $clean = [];
    foreach ( $ids as $id ) {
        $clean[] = sanitize_text_field( $id );
    }

    return $clean;
}

// NEGATIVE: request-derived array looped for output only
function output_only( WP_REST_Request $request ) {
    $body = json_decode( $request->get_body() );
    $items = $body->items ?? [];

    foreach ( $items as $item ) {
        echo esc_html( $item );
    }
}
