<?php

require '../lib.php';

$api = new InfoApi();
$input = $api->detectRequestBody();

if (isset($input['idanexo_etapa_proceso'])) {
    $json = $api->delete_Anexo_etapa_proceso($input['idanexo_etapa_proceso']);
} else {
    $json = "No se enviaron los parametros correctos";
}

echo json_encode($json);
