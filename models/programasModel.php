<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class programasModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getProgramas($record_c)
    {
        $sql='SELECT P.id, P.codigo, P.nombre, convert(varchar, P.nota) as nota
            FROM h2h_programa P
            JOIN h2h_programaOpc PO ON (P.id=PO.idprog)
            WHERE PO.record_c="'.$record_c.'"
            GROUP BY P.id, P.codigo, P.nombre, convert(varchar, P.nota)';
        
        $datos= $this->_db->consulta($sql);
        if($this->_db->numRows($datos)>0)
        {
            return $this->_db->fetchAll($datos);
        }
        else
        {
            return false;
        }
    }
}