<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class Funciones
{
    public static function creaHoteles($columnasSP){



                            if (!empty($columnasSP["codHotel_1"]) && trim($columnasSP["codHotel_1"])!="" && $columnasSP["codHotel_1"]!=null) {

                                    $xmlHoteles[] = array( 'codigo' => $columnasSP["codHotel_1"],
                                                                                    'nombre' => $columnasSP["hotel_1"],
                                                                                    'ciudad' => $columnasSP["codPlanAlimenticio_1"],
                                                                                    'tipoH' => 	$columnasSP["TipoHabitacion_1"],
                                                                                    'PA' => 	$columnasSP["PlanAlimenticio_1"],
                                                                                    'foto_1' => 'foto_1',
                                                                                    'foto_2' => 'foto_2',
                                                                                    'foto_3' => 'foto_3',
                                                                                    'foto_4' => 'foto_4'
                                                                            );
                            }

                            if (!empty($columnasSP["codHotel_2"]) && trim($columnasSP["codHotel_2"])!="" && $columnasSP["codHotel_2"]!=null) {

                                    $xmlHoteles[] = array( 'codigo' => $columnasSP["codHotel_2"],
                                                                                    'nombre' => $columnasSP["hotel_2"],
                                                                                    'ciudad' => $columnasSP["codPlanAlimenticio_2"],
                                                                                    'tipoH' => 	$columnasSP["TipoHabitacion_2"],
                                                                                    'PA' => 	$columnasSP["PlanAlimenticio_2"],
                                                                                    'foto_1' => 'foto_1',
                                                                                    'foto_2' => 'foto_2',
                                                                                    'foto_3' => 'foto_3',
                                                                                    'foto_4' => 'foto_4'
                                                            );
                            }

                            if (!empty($columnasSP["codHotel_3"]) && trim($columnasSP["codHotel_3"])!="" && $columnasSP["codHotel_3"]!=null) {

                                    $xmlHoteles[] = array( 'codigo' => $columnasSP["codHotel_3"],
                                                                                    'nombre' => $columnasSP["hotel_3"],
                                                                                    'ciudad' => $columnasSP["codPlanAlimenticio_3"],
                                                                                    'tipoH' => 	$columnasSP["TipoHabitacion_3"],
                                                                                    'PA' => 	$columnasSP["PlanAlimenticio_3"],
                                                                                    'foto_1' => 'foto_1',
                                                                                    'foto_2' => 'foto_2',
                                                                                    'foto_3' => 'foto_3',
                                                                                    'foto_4' => 'foto_4'
                                                            );

                            }

                            if (!empty($columnasSP["codHotel_4"]) && trim($columnasSP["codHotel_4"])!="" && $columnasSP["codHotel_4"]!=null) {

                                    $xmlHoteles[] = array( 'codigo' => $columnasSP["codHotel_4"],
                                                                                    'nombre' => $columnasSP["hotel_4"],
                                                                                    'ciudad' => $columnasSP["codPlanAlimenticio_4"],
                                                                                    'tipoH' => 	$columnasSP["TipoHabitacion_4"],
                                                                                    'PA' => 	$columnasSP["PlanAlimenticio_4"],
                                                                                    'foto_1' => 'foto_1',
                                                                                    'foto_2' => 'foto_2',
                                                                                    'foto_3' => 'foto_3',
                                                                                    'foto_4' => 'foto_4'
                                                            );
                            }

                            if (!empty($columnasSP["codHotel_5"]) && trim($columnasSP["codHotel_5"])!="" && $columnasSP["codHotel_5"]!=null) {	

                                    $xmlHoteles[] = array( 'codigo' => $columnasSP["codHotel_5"],
                                                                                    'nombre' => $columnasSP["hotel_5"],
                                                                                    'ciudad' => $columnasSP["codPlanAlimenticio_5"],
                                                                                    'tipoH' => 	$columnasSP["TipoHabitacion_5"],
                                                                                    'PA' => 	$columnasSP["PlanAlimenticio_5"],
                                                                                    'foto_1' => 'foto_1',
                                                                                    'foto_2' => 'foto_2',
                                                                                    'foto_3' => 'foto_3',
                                                                                    'foto_4' => 'foto_4'
                                                            );
                            }


                            return $xmlHoteles;
    }

    public static function creaValores($columnasSP){
            $xmlValores = array(	'valorHabitacion_1' => $columnasSP["vHab_1"],
                                                            'tipoHabitacion_1'	=> $columnasSP["tipoHab_1"],
                                                            'valorHabitacion_2' => $columnasSP["vHab_2"],
                                                            'tipoHabitacion_2'	=> $columnasSP["tipoHab_2"],
                                                            'valorHabitacion_3' => $columnasSP["vHab_3"],
                                                            'tipoHabitacion_3'	=> $columnasSP["tipoHab_3"]

                                                            );

            return $xmlValores;
    }

    public static function creaProgramas($columnasSP){

            return array(	'programa' => Funciones::creaPrograma($columnasSP),//$xmlPrograma, 
                                            'vigencia' => "funciona",//$xmlVigencia,
                                            'incluye'  => $columnasSP["KEY_"]	
                                    );
    }

    public static function creaPrograma($columnasSP){
            return array('codigo' => mb_convert_encoding($columnasSP["codigoPRG"], "UTF-8"),
                                    'nombre' => mb_convert_encoding($columnasSP["nombrePRG"], "UTF-8"),
                                    'nota'	  => mb_convert_encoding($columnasSP["notaPRG"], "UTF-8"),
                                    'url_itinerario' => mb_convert_encoding($columnasSP["itinerario"], "UTF-8")
                                    );
    }
}