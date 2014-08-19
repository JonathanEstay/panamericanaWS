<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class Database
{
    protected $_conexion;
    
    public function __construct() {
        $this->_conexion= mysql_connect(DB_HOST, DB_USER, DB_PASS);
        
        if(!empty($this->_conexion))
        {
            $bd= mysql_select_db(DB_NAME, $this->_conexion);
            if($bd!=1)
            {
                throw new Exception('Base de datos no encontrada');
            }
            else
            {
                //@mysql_set_charset('UTF-8',$this->_conexion);
                return TRUE;
            }
        }
        else
        {
            throw new Exception('No se pudo conectar a la Base de datos');
        }
    }
    
    public function consulta($query)
    {
        $rs= mysql_query($query, $this->_conexion);
        if(empty($rs))
        {
           return FALSE; 
        }
        else
        {
            return $rs;
        }
    }
    
    public function fetchAll($consulta)
    {
        $arrayFetch=array();
        while($reg = mysql_fetch_array($consulta))
        {
                $arrayFetch[]= $reg;
        }

        return $arrayFetch;
    }
    
    
    public function numRows($consulta)
    {
        return mysql_num_rows($consulta);
    }


    public function closeConex()
    {
        return mysql_close($this->conexion);
    }
}