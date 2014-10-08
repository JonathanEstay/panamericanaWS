<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class reservaModel extends Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function getPackages($codPRG)
    {
        $sql='SELECT * FROM packages WHERE codigo = "'.$codPRG.'" ';
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
    
    public function getFile($nFile)
    {
        $sql='SELECT [num_file], [tipof], [n_coti], CONVERT(Nvarchar(10), fecha, 103) as fecha, '
                . 'CONVERT(Nvarchar(10), f_viaje, 103) as f_viaje, [npax], [agencia], [nompax], [naciona], [ope], '
                . '[ciudad], [pais], [moneda], [cambio], [comag], [tcomi], [estado], [neto], [ajuste], [tticket], '
                . '[totventa], [totalco], [totpag], [vage], [datos], [notas], [fecontab], [ftkt], [autmod], [estnul], '
                . '[efactu], [totfac], [tipred], [totnc], [cfinal], [tt], [vta_age], [au_mg_me], [promoto], [feccierre], '
                . '[diacierre], [montodesciva], [MRK], [vta_comagdes], [Referencia], [NegocioSigav], [atipoa], [Por_comi], '
                . '[Tipo_comi], [Por_comip], [Tipo_comip], [area], [id_nodo] '
                . 'FROM file_ '
                . 'WHERE num_file = '.$nFile;        
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
    
    public function getDetFile($nFile)
    {
        $sql= 'SELECT num_file, codigo, nombre, CONVERT(Nvarchar(10), in_, 103) as in_, CONVERT(Nvarchar(10), out, 103) as out, pax_s, pax_d, pax_t, pax_q, pax_a, pax_i, pax_c, pax_ca, pax_c2 '
            . 'FROM det_file '
            . 'WHERE num_file = "'.$nFile.'" AND NOT(codigo = "CGO") '
            . 'ORDER BY lin ASC ';
        
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
    
    public function getDetBloq($codBloq, $nFile)
    {
        $sql = 'SELECT nompax, rut, CONVERT(Nvarchar(10), fchild,103) as fchild, ninfant, rut_inf, CONVERT(Nvarchar(10), finfant, 103) as finfant, tp, tipo_pax FROM det_bloq WHERE record_c = "'.$codBloq.'" and num_file = '.$nFile;
        
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
    
    
    public function getBloqueos($codBloq)
    {
        $sql = 'SELECT * FROM bloqueos WHERE record_c = "'.$codBloq.'" ';
        //echo $sql;
        
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