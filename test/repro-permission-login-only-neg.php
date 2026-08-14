<?php
// NEGATIVE: permission callbacks WITH capability checks must NOT fire
class Safe_Route {
    protected $auth = true;
    protected $current_user_id;

    public function permission_callback( WP_REST_Request $request ): bool {
        $this->current_user_id = get_current_user_id();
        return $this->current_user_id > 0 && current_user_can( 'manage_options' );
    }
}

function safe_permission_callback( $request ) {
    return get_current_user_id() > 0 && user_can( get_current_user_id(), 'edit_posts' );
}

add_action( 'rest_api_init', function () {
    register_rest_route(
        'demo/v1',
        '/admin/',
        [
            'methods'             => 'POST',
            'callback'            => 'cb',
            'permission_callback' => function () {
                if ( ! is_user_logged_in() ) {
                    return false;
                }
                return current_user_can( 'manage_options' );
            },
        ]
    );
} );

// NEGATIVE: non-permission functions doing plain login checks are out of scope
function helper_checks_login() {
    return get_current_user_id() > 0;
}
