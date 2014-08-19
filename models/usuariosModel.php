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
        $sql="SELECT * FROM usuarios WHERE clave='".$user."' ";
        
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
}