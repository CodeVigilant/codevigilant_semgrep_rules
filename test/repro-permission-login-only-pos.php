<?php
// POSITIVE: base-class permission_callback that only checks login status
// (variant A/D: get_current_user_id() / property check, no capability check)
class Route {
    protected $auth = true;
    protected $current_user_id;

    public function permission_callback( WP_REST_Request $request ): bool {
        $this->current_user_id = get_current_user_id();
        if ( $this->auth ) {
            return $this->current_user_id > 0;
        }
        return true;
    }
}

class Get_Stats extends Route {
    public function get_methods(): array {
        return [ 'GET' ];
    }
}

add_action( 'rest_api_init', function () {
    register_rest_route(
        'demo/v1',
        '/stats/',
        [
            'methods'             => 'GET',
            'callback'            => [ new Get_Stats(), 'callback' ],
            'permission_callback' => [ new Get_Stats(), 'permission_callback' ],
        ]
    );
} );

// POSITIVE variant C: is_user_logged_in() as the only check
function my_route_permission_callback( $request ) {
    return is_user_logged_in();
}

register_rest_route( 'demo/v1', '/logs', [
    'methods'             => 'GET',
    'callback'            => 'handler',
    'permission_callback' => 'my_route_permission_callback',
] );

// POSITIVE variant E: login gate returning true
function gate_permission_callback( $request ) {
    $uid = get_current_user_id();
    if ( $uid > 0 ) {
        return true;
    }
    return false;
}

register_rest_route( 'demo/v1', '/admin-ish', [
    'methods'             => 'POST',
    'callback'            => 'handler2',
    'permission_callback' => 'gate_permission_callback',
] );
