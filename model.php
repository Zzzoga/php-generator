<?php
function get_image_size($code, $config) {
    $mysqli = new mysqli(
        $config['db_host'],
        $config['db_user'],
        $config['db_pass'],
        $config['db_name']
    );
    if ($mysqli->connect_errno) return false;
    $stmt = $mysqli->prepare("SELECT width, height FROM image_sizes WHERE code = ?");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $stmt->bind_result($w, $h);
    $result = $stmt->fetch() ? ['width'=>$w, 'height'=>$h] : false;
    $stmt->close();
    $mysqli->close();
    return $result;
}