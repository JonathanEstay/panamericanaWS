<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

abstract class Controller
{
    protected $_view;
    
    public function __construct() {
        $this->_view= new view(new Request());
    }
    
    abstract public function index();
    
    protected function loadModel($modelo)
    {
        $modelo= $modelo . 'Model';
        $rutaModelo= ROOT . 'models' . DS . $modelo . '.php';
        
        if(is_readable($rutaModelo))
        {
            require_once $rutaModelo;
            $modelo= new $modelo;
            return $modelo; //retorna la instancia del modelo
        }
        else
        {
            throw new Exception('Error al leer el modelo: ' . $rutaModelo);
        }
    }
    
    protected function getTexto($clave)
    {
        if(isset($_POST[$clave]) && !empty($_POST[$clave]))
        {
            $_POST[$clave]= htmlspecialchars($_POST[$clave], ENT_QUOTES);
            return trim($_POST[$clave]);
        }
    }
    
    protected function getInt($clave)
    {
        if(isset($_POST[$clave]) && !empty($_POST[$clave]))
        {
            $_POST[$clave]= filter_input(INPUT_POST, $clave, FILTER_VALIDATE_INT);
            return trim($_POST[$clave]);
        }
        
        return 0;
    }
    
    
    protected function redireccionar($ruta = false)
    {
        if($ruta){
            header('location:' . BASE_URL . $ruta);
            exit;
        }
        else{
            header('location:' . BASE_URL);
            exit;
        }
    }
    
    
    protected function getSql($clave)
    {
        if(isset($_POST[$clave]) && !empty($_POST[$clave])){
            $_POST[$clave] = strip_tags($_POST[$clave]);
            
            if(!get_magic_quotes_gpc()){
                $_POST[$clave] = mysql_escape_string($_POST[$clave]);
            }
            
            return trim($_POST[$clave]);
        }
    }
    
    protected function getAlphaNum($clave)
    {
        if(isset($_POST[$clave]) && !empty($_POST[$clave])){
            $_POST[$clave] = (string) preg_replace('/[^A-Z0-9_]/i', '', $_POST[$clave]);
            return trim($_POST[$clave]);
        }
        
    }
    
    protected function getLibrary($libreria)
    {
        $rutaLibreria = ROOT . 'libs' . DS . $libreria . '.php';
        
        if(is_readable($rutaLibreria)){
            require_once $rutaLibreria;
        }
        else{
            throw new Exception('Error de libreria');
        }
    }
    
    protected function loadController($controller)
    {
        $rutaLibreria = ROOT . 'controllers' . DS . $controller . '.php';
        
        if(is_readable($rutaLibreria)){
            require_once $rutaLibreria;
        }
        else{
            throw new Exception('Error de loadController');
        }
    }
    
    protected function filtrarInt($int)
    {
        $int = (int) $int;
        
        if(is_int($int)){
            return $int;
        }
        else{
            return 0;
        }
    }
}