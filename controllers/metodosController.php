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
                    $mC_cntPrg= trim($detBloq["cnt_prg"]);
                    
                    $mC_ciudad= mb_convert_encoding(trim($detBloq["ciudad"]), "UTF-8");
                    $mC_pais= mb_convert_encoding(trim($detBloq["pais"]), "UTF-8");
                    
                    $xmlListadoBloqueos[]= array(
                        "record_c" => $mC_record_c,
                        "espacios" => $mC_espa,
                        "fecha_vuelo" => $mC_fecha_vuelo,
                        "fecha_tope" => $mC_fecha_tope,
                        "cod_prov" => $mC_cod_prov,
                        "hora_limi" => $mC_hora_limi,
                        "ciudad" => $mC_ciudad,
                        "pais" => $mC_pais,
                        "tramo" => $mC_tramo,
                        "notas" =>  $mC_notas,

                        "id_prog" => $mC_idProg,
                        "codigo_prog" => $mC_codigoProg,
                        "nombre_prog" => $mC_nombreProg,
                        "nombre_ope" => $mC_nombreOpe,
                        "CUANTOS" => $mC_cuantos,
                        "codigo_oris" => $mC_codOris,
                        "cnt_prg" => $mC_cntPrg
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
                    $mC_hasta= trim($detPRG["hasta"]);
                    $mC_iti= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["Itinerario"]), "UTF-8"));
                    $mC_itiVuelo= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["itinerarioVuelo"]), "UTF-8"));
                    $mC_notaPRG= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["notaPRG"]), "UTF-8"));
                    //$mC_notaPRG= str_replace('<br />', '\n', trim($detPRG["notaPRG"]));
                    $mC_notaOPC= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["notaOPC"]), "UTF-8"));
                    $mC_clave= trim($detPRG["clave"]);
                    $mC_recordC= trim($detPRG["record_c"]);
                    $mC_tramo= str_replace('<br />', '\n', mb_convert_encoding(trim($detPRG["Tramo"]), "UTF-8"));
                    
                    $mC_tipo_hab_1= trim($detPRG["tipoHab_1"]);
                    $mC_tipo_hab_2= trim($detPRG["tipoHab_2"]);
                    $mC_tipo_hab_3= trim($detPRG["tipoHab_3"]);
                    
                    
                    $mC_single= trim($detPRG["single"]);
                    $mC_doble= trim($detPRG["doble"]);
                    $mC_triple= trim($detPRG["triple"]);
                    $mC_qua= trim($detPRG["qua"]);
                    $mC_chd= trim($detPRG["chd"]);
                    $mC_chd2= trim($detPRG["chd2"]);
                    $mC_inf= trim($detPRG["inf"]);
                    $mC_PF= trim($detPRG["PF"]);
                    $mC_PxP= trim($detPRG["PxP"]);
                    
                    
                    
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
                        "hasta" => $mC_hasta,
                        "itinerario" => $mC_iti,
                        "itinerario_vuelo" => $mC_itiVuelo,
                        "nota_prg" => $mC_notaPRG,
                        //CDATA: "nota_opc" => '<item><width>123</width><height>345</height>07.<length>098</length><isle>'.$mC_notaOPC.'</isle></item>',
                        "nota_opc" => $mC_notaOPC,
                        "clave" => $mC_clave,
                        "record_c" => $mC_recordC,
                        "tramo" => $mC_tramo,
                        
                        "tipo_hab_1" => $mC_tipo_hab_1,
                        "tipo_hab_2" => $mC_tipo_hab_2,
                        "tipo_hab_3" => $mC_tipo_hab_3,
                        
                        "valores" => array(
                            "single" => $mC_single,
                            "doble" => $mC_doble,
                            "triple" => $mC_triple,
                            "qua" => $mC_qua,
                            "chd1" => $mC_chd,
                            "chd2" => $mC_chd2,
                            "inf" => $mC_inf,
                            "PF" => $mC_PF,
                            "PxP" => $mC_PxP
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
                
                
                $totVenta=(trim($detPRG["vHab_1"])+trim($detPRG["vHab_2"])+trim($detPRG["vHab_3"]));
                
                $xmlResponse= array(
                    "opcion" => $xmlOpciones,
                    "incluye" => $incluye,
                    "total_venta" => $totVenta
                    );
                
                return $xmlResponse;
            }
        }
    }
    
    
    public function usuariosRQ($args)
    {
        $args = (array)$args;

        $usuarioRQ= trim($args["Credenciales"]->usuario);
        $passwordRQ= trim($args["Credenciales"]->password);

        
        //Cargando el modelo usuarios
        $usuarios= $this->loadModel('usuarios');
        
        
        $var_getUser= $usuarios->getUsuario($usuarioRQ);
        if($var_getUser==false)
        {
            throw new SoapFault("Sin registros", null, "Usuario o password son incorrectos");
        }
        else
        {
            if(trim($var_getUser[0]['clave'])==$usuarioRQ)
            {
                if(trim($var_getUser[0]['pasword'])==$passwordRQ)
                {
                    $mC_nombre= trim($var_getUser[0]['nombre']);
                    $mC_codigo= trim($var_getUser[0]['codigo']);
                    $mC_agencia= trim($var_getUser[0]['agencia']);
                    $mC_atipoa= trim($var_getUser[0]['atipoa']);
                    $mC_id_agen= trim($var_getUser[0]['id_agen']);
                    $mC_email= trim($var_getUser[0]['email']);
                    $mC_email_opera= trim($var_getUser[0]['email_opera']);
                    
                    $xmlResponse= array(
                        "nombre" => $mC_nombre,
                        "codigo" => $mC_codigo,
                        "agencia" => $mC_agencia,
                        "atipoa" => $mC_atipoa,
                        "id_agen" => $mC_id_agen,
                        "email" => $mC_email,
                        "email_opera" => $mC_email_opera
                        );

                    return $xmlResponse;
                }
                else
                {
                    throw new SoapFault("Sin registros", null, "Usuario o password son incorrectos");
                }
            }
            else
            {
                throw new SoapFault("Sin registros", null, "Usuario o password son incorrectos");
            }
        }
        
    }
    
    
    public function programasRQ($args)
    {
        $args = (array)$args;
        
        $usuarioRQ= trim($args["Credenciales"]->usuario);
        $passwordRQ= trim($args["Credenciales"]->password);
        
        if($usuarioRQ=='panamericana' && $passwordRQ=='panaWS')
        {
            //Cargando el modelo usuarios
            $programas= $this->loadModel('programas');
            $record_cRQ= trim($args["Parametros"]->record_c);
            
            $listadoProg= $programas->getProgramas($record_cRQ);
            if($listadoProg==false)
            {
                throw new SoapFault("Sin registros", null, "No se encontraron programas asociados a ese record_c");
            }
            else
            {
                foreach($listadoProg as $detProg):
                    $mC_idProg= trim($detProg["id"]);
                    $mC_codigoProg= trim($detProg["codigo"]);
                    $mC_nombreProg= mb_convert_encoding(trim($detProg["nombre"]), "UTF-8");
                    $mC_nota= mb_convert_encoding(trim($detProg["nota"]), "UTF-8");
                    
                    
                    $xmlListadoProgramas[]= array(
                        "id" => $mC_idProg,
                        "codigo" => $mC_codigoProg,
                        "nombre" => $mC_nombreProg,
                        "nota" =>  $mC_nota
                        );
                endforeach;


                $xmlResponse= array("Programa" => $xmlListadoProgramas);
                return $xmlResponse;
            }
        }
        else
        {
            throw new SoapFault('Sin registros', null, 'Usuario o password son incorrectos');
        }
        
        
    }
    
    
    
    public function proceso_reservaRQ($args)
    {
        $args = (array)$args;

        $usuarioRQ= trim($args["Credenciales"]->usuario);
        $passwordRQ= trim($args["Credenciales"]->password);

        
        //Cargando el modelo usuarios
        $usuarios= $this->loadModel('usuarios');
        
        
        $var_getUser= $usuarios->getUsuario($usuarioRQ);
        if($var_getUser==false)
        {
            throw new SoapFault("Sin registros", null, "Usuario o password son incorrectos");
        }
        else
        {
            if(trim($var_getUser[0]['clave'])==$usuarioRQ)
            {
                if(trim($var_getUser[0]['pasword'])==$passwordRQ)
                {
                    
                    $MC_correoVendedor= trim($args["Parametros"]->correo_vendedor);
                    $MC_correoOculto= trim($args["Parametros"]->correo_oculto);
                    
                    $MC_Id_Agen= trim($var_getUser[0]['id_agen']);
                    $MC_codUsuario= trim($var_getUser[0]['clave']);
                    
                    $MC_Fecha_In_= trim($args["Parametros"]->Fecha_In_);
                    $MC_CodigoPrograma= trim($args["Parametros"]->CodigoPrograma);
                    $MC_CodigoBloqueo= trim($args["Parametros"]->CodigoBloqueo);
                    
                    $mC_TC_vage= trim($args["Parametros"]->vage);
                    $mC_TC_PxP= trim($args["Parametros"]->PxP);
                    
                    //exec WEB_ORIS_CREA_FILE_WS_TESTING
                    $sql="exec WEB_ORIS_CREA_FILE_WS '$MC_Id_Agen', '$MC_codUsuario', '$MC_Fecha_In_', '$MC_CodigoPrograma', '$MC_CodigoBloqueo' ";
                    
                    
                    /* HOTELES */
                    for($i=0; $i<5; $i++)
                    {
                        $MC_CodigoHotel= $args["Parametros"]->hoteles->hotel[$i]->CodigoHotel;
                        $MC_FechaIn= $args["Parametros"]->hoteles->hotel[$i]->FechaIn;
                        $MC_NumNoches= $this->IntConvert($args["Parametros"]->hoteles->hotel[$i]->NumNoches);
                        $MC_TipoH= $args["Parametros"]->hoteles->hotel[$i]->TipoH;
                        $MC_PA= $args["Parametros"]->hoteles->hotel[$i]->PA;
                        $MC_conVn= $args["Parametros"]->hoteles->hotel[$i]->conVn;
                        
                        $sql.=", '$MC_CodigoHotel', '$MC_FechaIn', '$MC_NumNoches', '$MC_TipoH', '$MC_PA', '$MC_conVn' ";
                    }
                    
                    
                    
                    
                    $MC_numHab= $this->IntConvert($args["Parametros"]->numHabitaciones);
                    $sql.=", '$MC_numHab' ";

                    $sql.=", '".$this->IntConvert($args["Parametros"]->numAdlHab_1)."' ";
                    $sql.=", '".$this->IntConvert($args["Parametros"]->numChildHab_1)."' ";
                    $sql.=", '".$this->IntConvert($args["Parametros"]->numInfHab_1)."' ";
                    
                    
                    if($MC_numHab==3)
                    {
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numAdlHab_2)."' ";
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numChildHab_2)."' ";
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numInfHab_2)."' ";
                        
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numAdlHab_3)."' ";
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numChildHab_3)."' ";
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numInfHab_3)."' ";
                    }
                    else if($MC_numHab==2)
                    {
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numAdlHab_2)."' ";
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numChildHab_2)."' ";
                        $sql.=", '".$this->IntConvert($args["Parametros"]->numInfHab_2)."' ";
                        $sql.=", '0', '0', '0' ";
                    }
                    else
                    {
                        $sql.=", '0', '0', '0', '0', '0', '0' ";
                    }
                    
                    
                    $MC_atipo= trim($args["Parametros"]->atipo);
                    $MC_moneda= trim($args["Parametros"]->Moneda);
                    $sql.=", '$MC_atipo', '$MC_moneda' ";
                    
                    

                    
                    /* PASAJEROS */
                    for($i=0; $i<10; $i++)
                    {
                        $MC_nombreAdl= mb_convert_encoding($args["Parametros"]->Pasajeros->Pasajero[$i]->NombrePasajero, "ISO-8859-1", "UTF-8");
                        $MC_rut= $args["Parametros"]->Pasajeros->Pasajero[$i]->Rut;
                        $MC_fNac= $args["Parametros"]->Pasajeros->Pasajero[$i]->F_nacimiento;
                        $MC_tipoPas= $args["Parametros"]->Pasajeros->Pasajero[$i]->TipoPasajero;
                        
                        $MC_nombreInf= mb_convert_encoding($args["Parametros"]->Pasajeros->Pasajero[$i]->NombrePasajero_Inf, "ISO-8859-1", "UTF-8");
                        $MC_rutInf= $args["Parametros"]->Pasajeros->Pasajero[$i]->Rut_Inf;
                        $MC_fNacInf= $args["Parametros"]->Pasajeros->Pasajero[$i]->F_nacimiento_Inf;
                        
                        $MC_tratoPax= $args["Parametros"]->Pasajeros->Pasajero[$i]->tratoPax;
                        
                        
                        $sql.=", '$MC_nombreAdl', '$MC_rut', '$MC_fNac', '$MC_tipoPas', '$MC_nombreInf', '$MC_rutInf', '$MC_fNacInf', '$MC_tratoPax' ";
                    }
                    
                    
                    $sql.=", '".trim($args["Parametros"]->tipoHabitaciones_1)."', '".trim($args["Parametros"]->tipoHabitaciones_2)."', '".trim($args["Parametros"]->tipoHabitaciones_3)."' ";
                    
                    
                    $sql.=", '".trim($args["Parametros"]->clave)."', '".mb_convert_encoding(trim($args["Parametros"]->datos), "ISO-8859-1", "UTF-8")."', '".trim($args["Parametros"]->totventa)."' ";
                    
                    
                    
                    
                    $sql.=", '".$mC_TC_vage."', '".$MC_correoVendedor."' ";
                    //echo $sql; exit; 
                    
                    
                    
                    /*$strHab=array();
                    if (trim($args["Parametros"]->tipoHabitaciones_1)!='')
                    {
                        $strHab[]= trim($args["Parametros"]->tipoHabitaciones_1);
                    }
                    if (trim($args["Parametros"]->tipoHabitaciones_2)!='')
                    {
                        $strHab[]= trim($args["Parametros"]->tipoHabitaciones_2);
                    }
                    if (trim($args["Parametros"]->tipoHabitaciones_3)!='')
                    {
                        $strHab[]= trim($args["Parametros"]->tipoHabitaciones_3);
                    }*/
                    
                    
                    
                    $procesoReserva= $usuarios->exeSP($sql);
                    if($procesoReserva!=false)
                    {
                        $mC_TC_codigo= trim($procesoReserva[0]["CODIGO"]);
                        $mC_TC_mensaje= mb_convert_encoding(trim($procesoReserva[0]["MENSAJE"]), "UTF-8");
                        $mC_TC_file= trim($procesoReserva[0]["FILE"]);
                        $mC_HTML='';
                        
                        
                        //RESERVA EXITOSA
                        if($mC_TC_codigo==1)
                        {
                            //CARGA MODELO DE RESERVA
                            $LM_reserva= $this->loadModel('reserva');
                            //$mC_TC_file='190306'; $MC_CodigoBloqueo='2014FLN019';
                            
                            
                            //TRAER DATOS DE LA TABLA FILE
                            $datosFile= $LM_reserva->getFile($mC_TC_file);
                            if($datosFile!=false)
                            {   
                                //PARSEA INFORME HTML
                                include ROOT . 'views' . DS . 'parseInforme.php';

                                $this->getLibrary('class.phpmailer');
                                $mail = new PHPMailer();
                                $mail->IsSMTP();
                                $mail->Host = trim("190.196.23.232");
                                $mail->Port = 25;
                                $mail->From = 'panamericana@online.panamericanaturismo.cl';
                                $mail->FromName = 'Panamericana Online';
                                $mail->CharSet = 'UTF-8';
                                $mail->Subject = 'Confirmacion de reserva online: '.$mC_TC_file;
                                $mail->MsgHTML($mC_HTML);
                                
                                //$mail->AltBody = 'Su servidor de correo no soporta html';
                                $mail->AddAddress($MC_correoVendedor, "");
                                //$mail->AddAddress("destino2@correo.com","Nombre 02"); 

                                $mail->AddCC(trim($var_getUser[0]['email']));
                                $mail->AddCC(trim($var_getUser[0]['email_opera']));
                                
                                $mail->AddBCC($MC_correoOculto);
                                
                                $mail->SMTPAuth = true;
                                $mail->Username = trim("online@panamericanaturismo.cl");
                                $mail->Password = trim("Fe90934");
                                $mail->Send();
                            }
                            else
                            {
                                $mC_HTML='Error al enviar el mail';
                            }
                        }
                        
                        
                        $xmlResponse= array(
                            "CODIGO" => $mC_TC_codigo,
                            "MENSAJE" => $mC_TC_mensaje,
                            "FILE" => $mC_TC_file,
                            "HTML" => $mC_HTML
                        );
                        
                        return $xmlResponse;
                    }
                    else
                    {
                        throw new SoapFault("Sin registros", null, "Error al realizar la reserva");
                    }
                    
                }
                else
                {
                    throw new SoapFault("Sin registros", null, "Usuario o password son incorrectos");
                }
            }
            else
            {
                throw new SoapFault("Sin registros", null, "Usuario o password son incorrectos");
            }
        }
    }
}
?>