<?php
// NEGATIVE: request path sanitized before file read
$name = sanitize_file_name($_GET['file']);
$path = wp_upload_dir()['basedir'] . '/' . $name;
$data = file_get_contents($path);
echo esc_html($data);
