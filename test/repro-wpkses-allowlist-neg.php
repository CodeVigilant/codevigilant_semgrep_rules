<?php
// NEGATIVE repro 1: properly escaped output — must NOT fire.
function render_escaped()
{
	echo esc_html($_GET['q']);
}

// NEGATIVE repro 2: wp_kses() on non-tainted (trusted) data — must NOT fire.
function render_trusted()
{
	$trusted = get_post_field('post_content', 1);
	echo wp_kses($trusted, wp_kses_allowed_html('post'));
}

// NEGATIVE repro 3: tainted input sanitized before reaching wp_kses() — must NOT fire.
function render_sanitized()
{
	$clean = sanitize_text_field($_GET['q']);
	echo wp_kses($clean, wp_kses_allowed_html('post'));
}
