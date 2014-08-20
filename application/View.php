<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class View
{
    private $_controlador;
    
    public function __construct(Request $peticion) { //$peticion es un objeto de Request
        $this->_controlador= $peticion->getControlador();
    }
    
    public function renderizaWSDL($vista)
    {
        $rutaView= ROOT . 'public' . DS . $vista . '.wsdl';
        if(is_readable($rutaView))
        {
            header('Content-Type: application/xml; charset=utf-8');
            header('Content-Disposition: inline; filename="'.$rutaWSDL.'"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            echo file_get_contents($rutaView);
        }
        else
        {
            throw new Exception('Error de vista WSDL');
        }
    }
    
}