<?php

require '../lib.php';

$api = new InfoApi();

$json = $api->listaUsuarios();

echo json_encode($json);
