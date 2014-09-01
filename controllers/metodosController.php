<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

class metodosController extends Controller
{
    public function __construct() {
        parent::__construct();
    }
    
    
    public function index(){}

    public function tomarEspaciosRQ($args){}


    public function listadoBloqueosRQ($args)
    {
        /* 	
        Rescatando Object enviado desde el WSDL hacia $args = (array)$args;
        $args["Credenciales"]->Usuario 
        */   
        $args = (array)$args;

        
        $usuarioRQ= trim($args["Credenciales"]->usuario);
        $passwordRQ= trim($args["Credenciales"]->password);
        
        $ciudad_RQ= trim($args["Parametros"]->ciudad);
        $fechaIn_RQ= trim($args["Parametros"]->fecha_in);
        $fechaOut_RQ= trim($args["Parametros"]->fecha_out);
                
        if($fechaIn_RQ)
        {
            $fechaIn_RQ= Funciones::invertirFecha($fechaIn_RQ, '/', '-');
        }
        
        if($fechaOut_RQ)
        {
            $fechaOut_RQ= Funciones::invertirFecha($fechaOut_RQ, '/', '-');
        }
        //echo $usuarioRQ.' - '.$passwordRQ.' - '.$ciudad_RQ.' - '.$fechaIn_RQ.' - '.$fechaOut_RQ; exit;
        
        //$usuarios= $this->loadModel('usuarios');
        $bloqueos= $this->loadModel('bloqueos');


        if($usuarioRQ=='panamericana' && $passwordRQ=='panaWS')
        {
            $var_getBloqueos= $bloqueos->getBloqueos($ciudad_RQ, $fechaIn_RQ, $fechaOut_RQ);
            if($var_getBloqueos==false)
            {
                throw new SoapFault("Sin registros", null, "No se encontraron bloqueos");
            }
            else
            {
                foreach($var_getBloqueos as $detBloq):
                    $mC_record_c= trim($detBloq["record_c"]);
                    $mC_espa= trim($detBloq["espa"]);
                    $mC_fecha_vuelo= trim($detBloq["fecha_vuelo"]);
                    $mC_fecha_tope= trim($detBloq["fecha_tope"]);
                    $mC_cod_prov= trim($detBloq["cod_prov"]);
                    $mC_hora_limi= trim($detBloq["hora_limi"]);
                    $mC_tramo= mb_convert_encoding(trim($detBloq["tramo"]), "UTF-8");
                
                    $mC_notas= mb_convert_encoding(trim($detBloq["notas"]), "UTF-8");
                    $mC_idProg= trim($detBloq["idProg"]);
                    $mC_codigoProg= trim($detBloq["programa"]);
                    $mC_nombreProg= mb_convert_encoding(trim($detBloq["nombre_prog"]), "UTF-8");
                    $mC_nombreOpe= mb_convert_encoding(trim($detBloq["nombreope"]), "UTF-8");
                    $mC_cuantos= trim($detBloq["CUANTOS"]);
                    $mC_codOris= trim($detBloq["codigoOris"]);
                    
                    
                    $xmlListadoBloqueos[]= array(
                        "record_c" => $mC_record_c,
                        "espacios" => $mC_espa,
                        "fecha_vuelo" => $mC_fecha_vuelo,
                        "fecha_tope" => $mC_fecha_tope,
                        "cod_prov" => $mC_cod_prov,
                        "hora_limi" => $mC_hora_limi,
                        "ciudad" => mb_convert_encoding($mC_ciudad, "UTF-8"),
                        "pais" => mb_convert_encoding($mC_pais, "UTF-8"),
                        "tramo" => $mC_tramo,
                        "notas" =>  $mC_notas,

                        "id_prog" => $mC_idProg,
                        "codigo_prog" => $mC_codigoProg,
                        "nombre_prog" => $mC_nombreProg,
                        "nombre_ope" => $mC_nombreOpe,
                        "CUANTOS" => $mC_cuantos,
                        "codigo_oris" => $mC_codOris
                        );
                endforeach;


                $xmlResponse= array("Bloqueo" => $xmlListadoBloqueos);
                return $xmlResponse;
            }

        }
        else
        {
                throw new SoapFault("Login Usuario", null, "Usuario o Password son Incorrectos.");
        }
    }
    
    
    public function detalleProgramaRQ($args)
    {
        $args = (array)$args;

        $usuarioRQ= trim($args["Credenciales"]->usuario);
        $passwordRQ= trim($args["Credenciales"]->password);

        $fechaIn_RQ= trim($args["Parametros"]->fecha_in);
        $recordC_RQ= trim($args["Parametros"]->record_c);
        $codigoPrg_RQ= trim($args["Parametros"]->codigo_prg);
        $idOpc_RQ= trim($args["Parametros"]->id_opc);


        if($fechaIn_RQ)
        {
            $fechaIn_RQ= Funciones::invertirFecha($fechaIn_RQ, '/', '-');
        }
        
        $sql='EXEC WEB_TraeProgramas_TC "'.$fechaIn_RQ.'", "'.$codigoPrg_RQ.'", "'.$idOpc_RQ.'", "'.$recordC_RQ.'"';
        
        $cntHab=count($args["Parametros"]->habitaciones->habitacion);
        
        if($cntHab>1)
        {
            for($i=0; $i<$cntHab; $i++)
            {
                $adultos_RQ= $this->IntConvert($args["Parametros"]->habitaciones->habitacion[$i]->adultos);
                $edadChild_1_RQ= $this->IntConvert(trim($args["Parametros"]->habitaciones->habitacion[$i]->edad_child_1));
                $edadChild_2_RQ= $this->IntConvert(trim($args["Parametros"]->habitaciones->habitacion[$i]->edad_child_2));
                $infant_RQ= $this->IntConvert(trim($args["Parametros"]->habitaciones->habitacion[$i]->infant));
                
                $sql.=', '.$adultos_RQ.', '.$edadChild_1_RQ.', '.$edadChild_2_RQ.', '.$infant_RQ.' ';
            }
        }
        else
        {
            $adultos_RQ= $this->IntConvert($args["Parametros"]->habitaciones->habitacion->adultos);
            $edadChild_1_RQ= $this->IntConvert(trim($args["Parametros"]->habitaciones->habitacion->edad_child_1));
            $edadChild_2_RQ= $this->IntConvert(trim($args["Parametros"]->habitaciones->habitacion->edad_child_2));
            $infant_RQ= $this->IntConvert(trim($args["Parametros"]->habitaciones->habitacion->infant));
            
            $sql.=', '.$adultos_RQ.', '.$edadChild_1_RQ.', '.$edadChild_2_RQ.', '.$infant_RQ.' ';
        }





        //echo $sql; exit;

        $bloqueos= $this->loadModel('bloqueos');
        
        if($usuarioRQ=='panamericana' && $passwordRQ=='panaWS')
        {
            $var_getDetalleProg= $bloqueos->exeSP($sql);
            if($var_getDetalleProg==false)
            //if($var_getDetalleProg!=false)
            {
                throw new SoapFault("Sin registros", null, "No se encontro detalle para el programa");
            }
            else
            {

                foreach($var_getDetalleProg as $detPRG):
                    $mC_idPRG= trim($detPRG["idPRG"]);
                    $mC_nombrePRG= trim($detPRG["nombrePRG"]);
                    $mC_ciudadPRG= trim($detPRG["ciudadPRG"]);
                    $mC_nochesPRG= trim($detPRG["nochesPRG"]);
                    $mC_idOPC= trim($detPRG["idOpcion"]);
                    $mC_moneda= trim($detPRG["moneda"]);
                    $mC_eMayor= trim($detPRG["edadMayor"]);
                    $mC_desde= trim($detPRG["desde"]);
                    $mC_iti= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["Itinerario"]), "UTF-8"));
                    $mC_itiVuelo= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["itinerarioVuelo"]), "UTF-8"));
                    $mC_notaPRG= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["notaPRG"]), "UTF-8"));
                    //$mC_notaPRG= str_replace('<br />', '\n', trim($detPRG["notaPRG"]));
                    $mC_notaOPC= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["notaOPC"]), "UTF-8"));
                    $mC_clave= trim($detPRG["clave"]);
                    $mC_recordC= trim($detPRG["record_c"]);
                    $mC_tramo= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["Tramo"]), "UTF-8"));
                    
                    
                    $mC_single= trim($detPRG["single"]);
                    $mC_doble= trim($detPRG["doble"]);
                    $mC_triple= trim($detPRG["triple"]);
                    $mC_qua= trim($detPRG["qua"]);
                    $mC_chd= trim($detPRG["chd"]);
                    $mC_chd2= trim($detPRG["chd2"]);
                    $mC_inf= trim($detPRG["inf"]);
                    $mC_PF= trim($detPRG["PF"]);
                    
                    
                    for($i=1; $i<=5; $i++)
                    {
                        $mC_codHotel= trim($detPRG["codHotel_".$i]);
                        $mC_nombreHot= trim($detPRG["hotel_".$i]);
                        $mC_codPA= trim($detPRG["codPlanAlimenticio_".$i]);
                        $mC_PA= trim($detPRG["PlanAlimenticio_".$i]);
                        $mC_codTH= trim($detPRG["codTipoHabitacion_".$i]);
                        $mC_TH= trim($detPRG["TipoHabitacion_".$i]);
                        
                        $mC_ciudad= trim($detPRG["ciudad_".$i]);
                        $mC_cat= trim($detPRG["cat_".$i]);
                        $mC_provee= trim($detPRG["provee_".$i]);
                        $mC_noches= trim($detPRG["noches_".$i]);
                        $mC_fechaIn= trim($detPRG["fechaIn_".$i]);
                        $mC_convn= trim($detPRG["convenio_".$i]);
                        
                        if(!empty($mC_codHotel))
                        {
                            $xmlHotel[]= array(
                                "codigo" => $mC_codHotel,
                                "nombre" => $mC_nombreHot,
                                "ciudad" => $mC_ciudad,
                                "fecha_in" => $mC_fechaIn,
                                "noches" => $mC_noches,
                                "categoria" => $mC_cat,

                                "proveedor" => $mC_provee,
                                "cod_pa" => $mC_codPA,
                                "p_alimenticio" => $mC_PA,
                                "cod_th" => $mC_codTH,
                                "t_habitacion" => $mC_TH,

                                "convenio" => $mC_convn
                                );
                            $xmlHoteles= array("hotel" => $xmlHotel);
                        }
                    }
                    unset($xmlHotel);
                    
                    $xmlOpciones[]= array(
                        "id_prg" => $mC_idPRG,
                        "nombre_prg" => $mC_nombrePRG,
                        "ciudad_prg" => $mC_ciudadPRG,
                        "noches_prg" => $mC_nochesPRG,
                        "id_opcion" => $mC_idOPC,
                        "moneda" => $mC_moneda,
                        "edad_mayor" => $mC_eMayor,
                        "desde" => $mC_desde,
                        "itinerario" => $mC_iti,
                        "itinerario_vuelo" => $mC_itiVuelo,
                        "nota_prg" => $mC_notaPRG,
                        //CDATA: "nota_opc" => '<item><width>123</width><height>345</height>07.<length>098</length><isle>'.$mC_notaOPC.'</isle></item>',
                        "nota_opc" => $mC_notaOPC,
                        "clave" => $mC_clave,
                        "record_c" => $mC_recordC,
                        "tramo" => $mC_tramo,
                        
                        "valores" => array(
                            "single" => $mC_single,
                            "doble" => $mC_doble,
                            "triple" => $mC_triple,
                            "qua" => $mC_qua,
                            "chd1" => $mC_chd,
                            "chd2" => $mC_chd2,
                            "inf" => $mC_inf,
                            "PF" => $mC_PF
                        ),
                        
                        "hoteles" => $xmlHoteles
                        );
                    
                    
                endforeach;
                
                
                
                
                
                $incluye=false;
                $sql='EXEC WEB_TraeDetalle_WS '.$mC_idPRG;
                $var_getIncluye= $bloqueos->exeSP($sql);
                if($var_getIncluye!=false)
                {
                    foreach($var_getIncluye as $columnInc):
                        $incluye.= mb_convert_encoding(trim($columnInc["nombre"]), 'UTF-8').'\n';
                    endforeach;
                }
                
                
                
                $xmlResponse= array(
                    "opcion" => $xmlOpciones,
                    "incluye" => $incluye
                    );
                
                return $xmlResponse;
            }
        }
    }
}
?>