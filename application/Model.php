<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class Model
{
    protected $_db;
    public function __construct() {
        $this->_db= new Database;
    }
}