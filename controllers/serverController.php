<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class serverController extends Controller
{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){}
    
    
    public function panamericana($wsdl=false)
    {
        if(!$wsdl)
        {
            $this->loadController('metodosController');
            $server = new SoapServer(ROOT . 'public' . DS . 'panamericana.wsdl');
            $server->setClass('metodosController');

            try
            {
                $server->handle();
            }
            catch (Exception $e)
            {
                $server->fault('Sender', $e->getMessage());
            }
        }
        else
        {
            $this->_view->renderizaWSDL('panamericana');
        }
    }
}

?>