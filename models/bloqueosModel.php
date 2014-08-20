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
    
    public function getBloqueos()
    {
        $sql="exec WEB_TraeProgramas_Oficial '".$ciudadRQ."', '".$fechaDesdeRQ."', '".$fechaHastaRQ."', '".$totalPasajeros."', '', '".$porHabitacion[0]["adultos"]."', '".$porHabitacion[0]["child_1"]."', '".$porHabitacion[0]["child_2"]."', '0', '".$porHabitacion[1]["adultos"]."', '".$porHabitacion[1]["child_1"]."', '".$porHabitacion[1]["child_2"]."', '0', '".$porHabitacion[2]["adultos"]."', '".$porHabitacion[2]["child_1"]."', '".$porHabitacion[2]["child_2"]."', '0'";
        
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