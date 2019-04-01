<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if(isset($input['anio'], $input['idcliente'])){
    $json = $api->get_proyecto($input['anio'], $input['idcliente']);
}else{
    $json = "Error, no se recibieron los datos correctos";
}


echo json_encode($json);
