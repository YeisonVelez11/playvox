<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['idproyecto'])){
    $json = $api->deteleProyecto($input['idproyecto']);
}else{
    $json = "Error, no se recibieron los datos correctos";
}


echo json_encode($json);
