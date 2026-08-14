<?php
// POSITIVE: request path used directly in file read
$path = $_GET['file'];
$data = file_get_contents($path);
echo $data;
