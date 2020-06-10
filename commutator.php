<?php
session_start();

require_once 'config.php';
require_once 'src/app/modules.php';

// Importa los modulos
require_once MOD . 'autentificacion/autentificacionModule.php';
require_once MOD . 'inicio/inicioModule.php';
require_once MOD . 'publicaciones/publicacionesModule.php';
require_once MOD . 'minegocio/minegocioModule.php';
require_once MOD . 'public/publicModule.php';
require_once MOD . 'negocio/negocioModule.php';

require_once 'src/app/functions.php';

// Verifica el host
$allowed_hosts = APP_HOSTS;
$host = $_SERVER['HTTP_HOST'];
if (!isset($host) || !in_array($host, $allowed_hosts)) {
    Modules::returnBadRequest("La dirección de host ($host) no esta autorizada");
}

// Recibe la solicitud
$patch = isset($_POST['patch']) ? htmlspecialchars($_POST['patch']) : null; // módulo/función
$patch = preg_split("@/@", $patch, null, PREG_SPLIT_NO_EMPTY);

// Verifica solicitud
if ($patch == null || count($patch) != 2) {
    Modules::returnBadRequest("El patch de la solicitud esta incorrecto");
}

$mod = $patch[0]; // Módulo
$fun = $patch[1]; // Función

// Verifica módulo
if (!isset(Modules::$ajax[$mod])) {
    Modules::returnBadRequest("No se encuentra el módulo ($mod)");
}

// Trae datos del módulo
$model = Modules::$ajax[$mod]['modelFile'];
$controller = Modules::$ajax[$mod]['controllerFile'];
$controllerName = Modules::$ajax[$mod]['controllerName'];
$authRequired = Modules::$ajax[$mod]['authRequired'];

/**
 * @var array
 */
$roles = Modules::$ajax[$mod]['roles'];

// Verifica auth
if ($authRequired == true && isset($_SESSION['user_id']) == false) {
    Modules::returnBadRequest("Se requiere autentificación para ejecutar funciones del módulo ($mod)");
}

// Verifica rol
if (count($roles) > 0) {
    $rol = isset($_SESSION['user_rol']) ? $_SESSION['user_rol'] : 0;
    if (!in_array($rol, $roles)) {
        Modules::returnBadRequest("No tiene permisos para acceder a este módulo ($mod)");
    }
}

// Trae archivos nesesarios para realizar la solicitud
require_once APP . 'database.php';
require_once MOD . $model;
require_once MOD . $controller;

// Llama la función
$object = new $controllerName();
if (is_callable(array($object, $fun))) {
    $object->$fun();
} else {
    Modules::returnBadRequest("No se puede ejecutar la función ($fun)");
}
