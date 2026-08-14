<?php
// POSITIVE repro: untrusted superglobal rendered through wp_kses() with the
// permissive 'post' allowlist -> reflected HTML/link/image injection (CWE-80).
function render_search_breadcrumb()
{
	$search_query = $_GET['s'];
	echo wp_kses($search_query, wp_kses_allowed_html('post'));
}

// POSITIVE repro: wp_kses_post() variant.
function render_comment()
{
	echo wp_kses_post($_POST['message']);
}
