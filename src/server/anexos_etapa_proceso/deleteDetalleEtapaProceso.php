<?php

require '../lib.php';

$api = new InfoApi();
$input = $api->detectRequestBody();

if (isset($input['iddetalle_etapa_proceso'])) {
    $json = $api->delete_detalle_etapa_proceso($input['iddetalle_etapa_proceso']);
} else {
    $json = "No se enviaron los parametros correctos";
}

echo json_encode($json);
