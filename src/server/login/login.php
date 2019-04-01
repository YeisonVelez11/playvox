<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

$usr = $input['user'];
$pwd = $input['pwd'];

if (isset($usr, $pwd)) {
    $json = $api->login($usr, $pwd);
    
    if (sizeof($json) != 0) {
        session_start();
        $_SESSION['idusuario'] = $json[0]['idusuario'];
    }
}

echo json_encode($json);
