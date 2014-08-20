<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

ini_set('display_errors', 1);
ini_set("soap.wsdl_cache_enabled", "0"); // Deshabilitando WSDL de la memoria cache
date_default_timezone_set('America/Santiago');

//header('Content-Type: text/html; charset=UTF-8');
//header('Content-Type: text/html; charset=ISO-8859-1');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);
define('APP_PATH', ROOT . 'application' . DS);

try
{
    require_once APP_PATH . 'Config.php';
    require_once APP_PATH . 'Request.php';
    require_once APP_PATH . 'Bootstrap.php';
    require_once APP_PATH . 'Controller.php';
    require_once APP_PATH . 'Model.php';
    require_once APP_PATH . 'Database.php';
    require_once APP_PATH . 'Funciones.php';
    require_once APP_PATH . 'View.php';

    Bootstrap::run(new Request);
}
catch (Exception $e)
{
    echo $e->getMessage();
}
?>