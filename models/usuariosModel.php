<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class usuariosModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getUsuario($user)
    {
        $user= addslashes($user);
        //$sql='SELECT * FROM usuarios WHERE clave="'.$user.'"';
        $sql='SELECT U.*, AG.*
            FROM usuarios U
            JOIN agenc_na AG ON (AG.id=U.id_agen)
            WHERE U.clave="'.$user.'" ';
        
        //echo $sql.' - '; exit;
        
        $detUser= $this->_db->consulta($sql);
        if($this->_db->numRows($detUser)>0)
        {
            return $this->_db->fetchAll($detUser);
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