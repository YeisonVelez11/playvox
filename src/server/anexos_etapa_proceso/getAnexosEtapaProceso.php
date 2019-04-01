<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

$json = $api->get_AnexosSoporteEntregable($input['iddetalle_etapa_proceso']);

echo json_encode($json);
