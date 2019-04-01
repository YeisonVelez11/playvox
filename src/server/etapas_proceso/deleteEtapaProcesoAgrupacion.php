<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();
if (isset($input['idagrupacion'])) {

    $json = $api->delete_etapa_proceso_agrupacion($input['idagrupacion']);
} else {
    $json = "error, no se recibieron los datos correctos";
}


echo json_encode($json);
