<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class indexController extends Controller
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){}
    
    
    public function PanamericanaServer()
    {
        $server = new SoapServer("Oris.wsdl");
        $server->setClass("Metodos");

        try
        {
            $server->handle();
        }
        catch (Exception $e)
        {
            $server->fault('Sender', $e->getMessage());
        }
    }
}

?>