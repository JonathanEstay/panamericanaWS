<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class Funciones
{
    public static function invertirFecha($fecha, $char, $newChar)
    {
        $datos = explode($char, $fecha);
        $fechaFinal = $datos[2].$newChar.$datos[1].$newChar.$datos[0];
        return $fechaFinal;
    }
    
    public static function getTipoMoneda($moneda)
    {
        if($moneda == 'D')
        {
             $newMon= 'USD';
        }
        elseif($moneda == 'P')
        {
            $newMon= '$';
        }
        elseif($moneda == 'E')
        {
            $newMon= 'EUR';
        }
        
        return $newMon;
    }
    
    public static function getStringHab($tipoHab)
    {
        if($tipoHab)
        {
            $stringHab=false;
            $tipos= array('SGL', 'DBL', 'TPL', 'QUA', 'DEP');
            $tiposText= array('Single', 'Doble', 'Triple', 'Quadruple', 'Departamento');
            
            for($i=0; $i<5; $i++)
            {
                $pos = strpos($tipoHab, $tipos[$i]);
                
                if ($pos !== false) { //encontrado
                    $stringHab= '<b>'.$tiposText[$i] . '</b>: {val} ';
                    break;
                }
            }
            
            return $stringHab;
        }
        else
        {
            return false;
        }
    }
}