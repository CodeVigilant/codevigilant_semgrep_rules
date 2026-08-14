<?php
// POSITIVE: loop over ids from decoded JSON body, per-item DB work, no cap
function status_handler( WP_REST_Request $request ) {
    $body = json_decode( $request->get_body() );
    $image_ids = $body->image_ids ?? null;

    if ( empty( $image_ids ) ) {
        return [];
    }

    foreach ( $image_ids as $image_id ) {
        $meta = get_post_meta( $image_id, 'image_optimizer_metadata', true );
        $output[ $image_id ] = $meta;
    }

    return $output;
}

// POSITIVE: object construction with the loop variable inside the loop
function bulk_handler( WP_REST_Request $request ) {
    $body = json_decode( $request->get_body() );
    $ids = $body->ids ?? [];

    foreach ( $ids as $id ) {
        $image = new Image_Meta( $id );
        $out[] = $image->get_status();
    }

    return $out;
}

// POSITIVE: superglobal-driven loop with wpdb queries
function cleanup_handler() {
    $items = $_POST;
    foreach ( $items as $item_id ) {
        $wpdb->query( "DELETE FROM {$wpdb->prefix}things WHERE id = $item_id" );
    }
}
