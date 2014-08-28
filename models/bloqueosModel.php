<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class bloqueosModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getBloqueos($ciudadRQ, $fechaDesdeRQ, $fechaHastaRQ)
    {
        if($ciudadRQ)
        {
            if($fechaDesdeRQ)
            {
                $sql="EXEC WS_getBloqueos '".$ciudadRQ."', '".$fechaDesdeRQ."', '".$fechaHastaRQ."'";
            }
            else
            {
                $sql="EXEC WS_getBloqueos '".$ciudadRQ."'";
            }
        }
        else
        {
            if($fechaDesdeRQ)
            {
                $sql="EXEC WS_getBloqueos NULL, '".$fechaDesdeRQ."', '".$fechaHastaRQ."'";
            }
            else
            {
                $sql="EXEC WS_getBloqueos";
            }
            
        }
        
        $bloqueos= $this->_db->consulta($sql);
        if($this->_db->numRows($bloqueos)>0)
        {
            return $this->_db->fetchAll($bloqueos);
        }
        else
        {
            return false;
        }
    }
    
    
    
    public function exeSP($sql)
    {
        //echo $sql; exit;
        $bloqueos= $this->_db->consulta($sql);
        if($this->_db->numRows($bloqueos)>0)
        {
            return $this->_db->fetchAll($bloqueos);
        }
        else
        {
            return false;
        }
    }
}