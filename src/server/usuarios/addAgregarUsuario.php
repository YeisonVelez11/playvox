<?php

require '../lib.php';

$api = new InfoApi();

$input = $api->detectRequestBody();

if (isset($input['apellido'])) {
    $apellido = $input['apellido'];
} else {
    $apellido = null;
}

if (isset($input['email'])) {
    $email = $input['email'];
} else {
    $email = null;
}
if (isset($input['empresa'])) {
    $empresa = $input['empresa'];
} else {
    $empresa = null;
}
if (isset($input['cargo'])) {
    $cargo = $input['cargo'];
} else {
    $cargo = null;
}
if (isset($input['acceso_plataforma'])) {
    $acceso_plataforma = $input['acceso_plataforma'];
} else {
    $acceso_plataforma = null;
}
if (isset($input['imagen'])) {
    $imagen = $input['imagen'];
} else {
    $imagen = null;
}

if (isset($input['contrasena'])) {
    $contrasena = $input['contrasena'];
} else {
    $contrasena = null;
}

if (isset($input['nombre'], $input['idtipo_usuario'])) {
    $json = $api->agregarUsuario($input['nombre'], $apellido, $email, $contrasena, $input['idtipo_usuario'], $empresa, $cargo, $acceso_plataforma, $imagen);
} else {
    $json = "error, no se recibieron los datos correctos";
}

echo json_encode($json);
