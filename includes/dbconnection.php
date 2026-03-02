<?php
$con = mysqli_connect('localhost', 'root', '', 'detsdb');
if (!$con) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    http_response_code(500);
    exit('A server error occurred.');
}

mysqli_set_charset($con, 'utf8mb4');
