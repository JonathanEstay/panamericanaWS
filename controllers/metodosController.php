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
    

    public function consulta_programaRQ($args){
            //Instanciando Objetos
            $functions= new Functions;
            $private_functions= new PrivateFunctions;


            /* 	
                    Rescatando Object enviado desde el WSDL   $args = (array)$args;
                    $args["Credenciales"]->Usuario 
            */
            $args = (array)$args;

            $idRQ = $args["Credenciales"]->id;
            $idAgenRQ = $args["Credenciales"]->idAgen;
            $userRQ = $args["Credenciales"]->usuario;
            $passRQ = $args["Credenciales"]->password;


            //declaracion Variables
            $var_openConex='false';
            $var_getConsultaPRG='false';
            $statusRut='false';


            //Conexion BD
            $var_openConex= $private_functions->openConexion($_SESSION["WS_sess_server_name_conex"], 
                                                                                                            $_SESSION["WS_sess_user_name_conex"], 
                                                                                                            $_SESSION["WS_sess_password_conex"], 
                                                                                                            $_SESSION["WS_sess_bd_conex"]);



            //Verifica User
                    $var_obtieneUser= $private_functions->getUsuarios($userRQ);

                    if(trim($var_obtieneUser)=="false")
                    {
                            throw new SoapFault("Login Usuario", null, "Usuario o Password son Incorrectos.");
                    }
                    else
                    {
                            foreach($var_obtieneUser as $columUser):
                                    $varUsuario		= trim($columUser["clave"]);
                                    $varPass		= trim($columUser["pasword"]);	
                            endforeach;

                            if($userRQ==$varUsuario && $passRQ==$varPass)
                            {
                                    //EJECUTANDO STORED PROCEDURE
                                    $var_obtienePERFIL= $private_functions->getPERFIL($userRQ, 'FILEWEB');
                                    foreach($var_obtienePERFIL as $columPERFIL):
                                            $varAcceso		= trim($columPERFIL["acceso"]);
                                    endforeach;

                                    if($varAcceso!= "S")
                                    {
                                            throw new SoapFault("Login Usuario", null, "Usuario NO puede crear reservas"); 
                                            exit;	
                                    }
                                    else
                                    {




                                            $ciudadRQ = $args["Detalle"]->ciudad;
                                            $fechaDesdeRQ = $functions->fechaEng($args["Detalle"]->fechaDesde,"/","-");
                                            $fechaHastaRQ = $functions->fechaEng($args["Detalle"]->fechaHasta,"/","-");
                                            $habitacionesRQ[] = $args["Detalle"]->habitacion;
                                            $habitacionesRQ = $habitacionesRQ[0];
                                            $totalPasajeros = 0;
                                            $totalChild = 0 ;
                                            $porHabitacion;
                                            $habitacionesCnt = 0;
                                            $habitacionesCnt = count($habitacionesRQ);
                                            if($habitacionesCnt>3)
                                            {
                                                    throw new SoapFault("Excede limite", null, "El Limite de htabitaciones es 3 , usted envio ".$habitacionesCnt.".");
                                                    exit;
                                            }
                                            if($habitacionesCnt>1)
                                            {
                                                    foreach($habitacionesRQ as $habitaciones)
                                                    {
                                                            if($habitaciones->adultos*1 < 1 || empty($habitaciones->adultos)){
                                                                    throw new SoapFault("Peticion erronea", null, "Debe incluir al menos 1 adulto por habitacion.");
                                                                    exit;
                                                                    }
                                                            $totalPasajeros +=  $habitaciones->adultos*1;
                                                            if(($habitaciones->child_1*1)>0){$totalPasajeros += 1;}
                                                            if(($habitaciones->child_2*1)>0){$totalPasajeros += 1;}
                                                            //$porHabitacion[] = array("adultos"=>$habitaciones->adultos,"child_1"=>$habitaciones->child_1,"child_2"=>$habitaciones->child_2);	
                                                            $porHabitacion[] = array("adultos"=>$habitaciones->adultos*1,
                                                                                                             "child_1"=>$habitaciones->child_1*1, 
                                                                                                             "child_2"=>$habitaciones->child_2*1, 
                                                                                                             "INF"=>$habitaciones->INF*1);
                                                    }
                                            }else{
                                                    $totalPasajeros +=  $habitacionesRQ->adultos*1;
                                                    if(($habitacionesRQ->child_1*1)>0){$totalPasajeros += 1;}
                                                    if(($habitacionesRQ->child_2*1)>0){$totalPasajeros += 1;}
                                                    //$porHabitacion[] = array("adultos"=>$habitaciones->adultos,"child_1"=>$habitaciones->child_1,"child_2"=>$habitaciones->child_2);	
                                                    $porHabitacion[] = array("adultos"=>$habitacionesRQ->adultos*1,
                                                                                                     "child_1"=>$habitacionesRQ->child_1*1,
                                                                                                     "child_2"=>$habitacionesRQ->child_2*1, 
                                                                                                     "INF"=>$habitacionesRQ->INF*1);

                                                    }


                                                    if($totalPasajeros>10){
                                                                    throw new SoapFault("Excede limite", null, "El Limite de pasajeros es 10 , usted envio ".$totalPasajeros.".");
                                                                    exit;
                                                            }




                                            if($var_openConex==false)
                                            {
                                                    $functions->getError('1');
                                            }
                                            else
                                            {	


                                                    //$sqlSP= "exec WEB_TraeProgramas_Oficial '".$ciudad."', '".$FechaDesde."', '".$FechaHasta."', 'Pasajeros',  ' ' ";
                                                    $sqlSP="exec WEB_TraeProgramas_Oficial '".$ciudadRQ."', '".$fechaDesdeRQ."', '".$fechaHastaRQ."', '".$totalPasajeros."', '', '".$porHabitacion[0]["adultos"]."', '".$porHabitacion[0]["child_1"]."', '".$porHabitacion[0]["child_2"]."', '0', '".$porHabitacion[1]["adultos"]."', '".$porHabitacion[1]["child_1"]."', '".$porHabitacion[1]["child_2"]."', '0', '".$porHabitacion[2]["adultos"]."', '".$porHabitacion[2]["child_1"]."', '".$porHabitacion[2]["child_2"]."', '0'";

                                                    $resultadoSP= $private_functions->consulta($sqlSP);
                                                    $resultadoArray= $private_functions->fetch_array($resultadoSP);

                                                    unset($arrayAux);
                                                    unset($xmlPrograma);
                                                    unset($xmlVigencia);
                                                    unset($xmlSalida);
                                                    unset($xmlOpcion);
                                                    unset($xmlHoteles);
                                                    unset($xmlValores);
                                            foreach ($resultadoArray as $columnasSP ) {
                                                    $sqlInsert =  "INSERT INTO  tmpConsultaProgramas VALUES(
                                                                            '".$columnasSP[KEY_]."',
                                                                            '".$userRQ."',
                                                                            '".$columnasSP[idPRG]."',
                                                                            '".$columnasSP[codigoPRG]."',
                                                                            '".$columnasSP[nombrePRG]."',
                                                                            '".$columnasSP[ciudadPRG]."',
                                                                            '".$columnasSP[nochesPRG]."',
                                                                            '".$columnasSP[idOpcion]."',
                                                                            '".$columnasSP[moneda]."',
                                                                            '".$columnasSP[vHab_1]."',
                                                                            '".$columnasSP[tipoHab_1]."',
                                                                            '".$columnasSP[vHab_2]."',
                                                                            '".$columnasSP[tipoHab_2]."',
                                                                            '".$columnasSP[vHab_3]."',
                                                                            '".$columnasSP[tipoHab_3]."',
                                                                            '".$columnasSP[PFamilia]."',
                                                                            '".$columnasSP[desde]."',
                                                                            '".$columnasSP[Itinerario]."',
                                                                            '".$columnasSP[itinerarioVuelo]."',
                                                                            '".$columnasSP[notaPRG]."',
                                                                            '".$columnasSP[notaOPC]."',
                                                                            '".$columnasSP[clave]."',
                                                                            '".$columnasSP[record_c]."',
                                                                            '".$columnasSP[Tramo]."',
                                                                            '".$columnasSP[Espacios]."',
                                                                            '".$columnasSP[codHotel_1]."',
                                                                            '".$columnasSP[hotel_1]."',
                                                                            '".$columnasSP[codPlanAlimenticio_1]."',
                                                                            '".$columnasSP[PlanAlimenticio_1]."',
                                                                            '".$columnasSP[codTipoHabitacion_1]."',
                                                                            '".$columnasSP[TipoHabitacion_1]."',
                                                                            '".$columnasSP[ciudad_1]."',
                                                                            '".$columnasSP[cat_1]."',
                                                                            '".$columnasSP[provee_1]."',
                                                                            '".$columnasSP[noches_1]."',
                                                                            '".str_replace('/', '-', $columnasSP[fechaIn_1])."',
                                                                            '".$columnasSP[convenio_1]."',
                                                                            '".$columnasSP[codHotel_2]."',
                                                                            '".$columnasSP[hotel_2]."',
                                                                            '".$columnasSP[codPlanAlimenticio_2]."',
                                                                            '".$columnasSP[PlanAlimenticio_2]."',
                                                                            '".$columnasSP[codTipoHabitacion_2]."',
                                                                            '".$columnasSP[TipoHabitacion_2]."',
                                                                            '".$columnasSP[ciudad_2]."',
                                                                            '".$columnasSP[cat_2]."',
                                                                            '".$columnasSP[provee_2]."',
                                                                            '".$columnasSP[noches_2]."',
                                                                            '".str_replace('/', '-', $columnasSP[fechaIn_2])."',
                                                                            '".$columnasSP[convenio_2]."',
                                                                            '".$columnasSP[codHotel_3]."',
                                                                            '".$columnasSP[hotel_3]."',
                                                                            '".$columnasSP[codPlanAlimenticio_3]."',
                                                                            '".$columnasSP[PlanAlimenticio_3]."',
                                                                            '".$columnasSP[codTipoHabitacion_3]."',
                                                                            '".$columnasSP[TipoHabitacion_3]."',
                                                                            '".$columnasSP[ciudad_3]."',
                                                                            '".$columnasSP[cat_3]."',
                                                                            '".$columnasSP[provee_3]."',
                                                                            '".$columnasSP[noches_3]."',
                                                                            '".str_replace('/', '-', $columnasSP[fechaIn_3])."',
                                                                            '".$columnasSP[convenio_3]."',
                                                                            '".$columnasSP[codHotel_4]."',
                                                                            '".$columnasSP[hotel_4]."',
                                                                            '".$columnasSP[codPlanAlimenticio_4]."',
                                                                            '".$columnasSP[PlanAlimenticio_4]."',
                                                                            '".$columnasSP[codTipoHabitacion_4]."',
                                                                            '".$columnasSP[TipoHabitacion_4]."',
                                                                            '".$columnasSP[ciudad_4]."',
                                                                            '".$columnasSP[cat_4]."',
                                                                            '".$columnasSP[provee_4]."',
                                                                            '".$columnasSP[noches_4]."',
                                                                            '".str_replace('/', '-', $columnasSP[fechaIn_4])."',
                                                                            '".$columnasSP[convenio_4]."',
                                                                            '".$columnasSP[codHotel_5]."',
                                                                            '".$columnasSP[hotel_5]."',
                                                                            '".$columnasSP[codPlanAlimenticio_5]."',
                                                                            '".$columnasSP[PlanAlimenticio_5]."',
                                                                            '".$columnasSP[codTipoHabitacion_5]."',
                                                                            '".$columnasSP[TipoHabitacion_5]."',
                                                                            '".$columnasSP[ciudad_5]."',
                                                                            '".$columnasSP[cat_5]."',
                                                                            '".$columnasSP[provee_5]."',
                                                                            '".$columnasSP[noches_5]."',
                                                                            '".str_replace('/', '-', $columnasSP[fechaIn_5])."',
                                                                            '".$columnasSP[convenio_5]."',
                                                                            '".$porHabitacion[0]["adultos"]."',
                                                                            '".$porHabitacion[0]["child_1"]."',
                                                                            '".$porHabitacion[0]["child_2"]."',
                                                                            '".$porHabitacion[0]["INF"]."',
                                                                            '".$porHabitacion[1]["adultos"]."',
                                                                            '".$porHabitacion[1]["child_1"]."',
                                                                            '".$porHabitacion[1]["child_2"]."',
                                                                            '".$porHabitacion[1]["INF"]."',
                                                                            '".$porHabitacion[2]["adultos"]."',
                                                                            '".$porHabitacion[2]["child_1"]."',
                                                                            '".$porHabitacion[2]["child_2"]."',
                                                                            '".$porHabitacion[2]["INF"]."',
                                                                            '".$fechaDesdeRQ."',
                                                                            '".$fechaHastaRQ."',
                                                                            '".$habitacionesCnt."',
                                                                            '".date("Y-m-d")."',
                                                                            '".date("H:i")."'

                                                                                            )";
                                                                    $private_functions->consulta($sqlInsert);


                                                    if (empty($columnasSP["KEY_"])) {


                                                                    foreach ($resultadoArray as $columnasSPDos) {

                                                                            if (!empty($columnasSPDos["KEY_"]) && ($columnasSP["idPRG"]==$columnasSPDos["idPRG"])) {

                                                                                    $xmlOpcion[] = array( 	'clave' => $columnasSPDos["KEY_"],
                                                                                                                                    'moneda' => $columnasSPDos["moneda"],
                                                                                                                                    'hotel' => Funciones::creaHoteles($columnasSPDos),
                                                                                                                                    'valores' => Funciones::creaValores($columnasSPDos),
                                                                                                                                    'nota' =>  $columnasSP['notaOPC']
                                                                                                                            );

                                                                                    if ($fechaAux!= $columnasSPDos["desde"])
                                                                                    {

                                                                                            $xmlSalida[] =  array(	'opcion' => $xmlOpcion,
                                                                                                                                            'itinerario_vuelo' =>"asdasdas"
                                                                                                                                    );

                                                                                            unset($xmlOpcion);
                                                                                            $fechaAux = $columnasSPDos["desde"];
                                                                                    }else{

                                                                                            $fechaAux = $columnasSPDos["desde"];

                                                                                    }


                                                                            }

                                                                    }//endForeach




                                                                    $xmlSalida[] = array(	'opcion' => $xmlOpcion,
                                                                                                                    'itinerario_vuelo' => str_replace("<text>","","<text><text><text>aaa</text></text></text>")

                                                                                    );
                                                                    $xmlPrograma = array(	'codigo' => $columnasSP["codigoPRG"],
                                                                                                            'nombre' => "nombre programa CDATA",
                                                                                                            'nota'	  => htmlentities("asdasd"),
                                                                                                            'url_itinerario' => $columnasSP["Itinerario"]
                                                                                                    );

                                                                    $xmlVigencia = array( 'salida' => $xmlSalida );

                                                                    $arrayAux = array(	'programa' => $xmlPrograma, 
                                                                                                    'vigencia' => $xmlVigencia,
                                                                                                    'incluye'  => $columnasSP["idPRG"]
                                                                                            );



                                                            $xmlProgramas[] = $arrayAux;
                                                            unset($arrayAux);
                                                            unset($xmlPrograma);
                                                            unset($xmlVigencia);
                                                            unset($xmlOpcion);
                                                            unset($xmlSalida);
                                                            unset($xmlHoteles);
                                                            unset($xmlValores);

                                                    }	







                                            }//endofreach


                                            return $xmlProgramas;

                                            }//End: $openConex


                                    }

                            }else{
                                    throw new SoapFault("Login Usuario", null, "Usuario o Password son Incorrectos.");
                            }
            $private_functions->closeConexion();
                    }

                    exit();
    }


    public function reservaProgramaRQ($args)
    {
            //Instanciando Objetos
            $functions= new Functions;
            $private_functions= new PrivateFunctions;


            /* 	
                    Rescatando Object enviado desde el WSDL   $args = (array)$args;
                    $args["Credenciales"]->Usuario 
            */
            $args = (array)$args;
            //$pasajerosArray= array();

            $usuarioRQ= trim($args["Credenciales"]->usuario);
            $passwordRQ= trim($args["Credenciales"]->password);
            $KEY_OPCION_RQ= trim($args["Parametros"]->KEY_OPCION);
            $datosPasajeroRQ[] = $args["Parametros"]->DatosDelPasajero;
            $datosPasajeroRQ = $datosPasajeroRQ[0];
            $cantiPas=0;
            $cantiPasTMP=0;

            $datosPasajeroCnt = count($datosPasajeroRQ);
            /*if($datosPasajeroCnt > 10)
            {
                    throw new SoapFault("Excede limite", null, "El Limite de pasajeros es 10 , usted ingreso ".$datosPasajeroCnt.".");
                    exit;
            }
            else*/ if($datosPasajeroCnt == 1)
            {
                    if(trim($datosPasajeroRQ->Tipo)!='')
                    {
                            $pasajerosArray[0][0]= $datosPasajeroRQ->Tipo;
                            $pasajerosArray[0][1]= $datosPasajeroRQ->Pasaporte;
                            $pasajerosArray[0][2]= $datosPasajeroRQ->Rut;
                            $pasajerosArray[0][3]= $datosPasajeroRQ->Nombre;
                            $pasajerosArray[0][4]= $datosPasajeroRQ->Apellidos;
                            $pasajerosArray[0][5]= $datosPasajeroRQ->Fecha_Nac;
                            $cantiPas++;
                    }
            }
            else
            {
                    foreach($datosPasajeroRQ as $datosP)
                    {
                            if(trim($datosP->Tipo)!='')
                            {
                                    $pasajerosArray[$cantiPasTMP][0]= $datosP->Tipo;
                                    $pasajerosArray[$cantiPasTMP][1]= $datosP->Pasaporte;
                                    $pasajerosArray[$cantiPasTMP][2]= $datosP->Rut;

                                    /*echo $pasajerosArray[$cantiPas][2].' - '.$datosP->Rut;
                                    exit;*/
                                    $pasajerosArray[$cantiPasTMP][3]= $datosP->Nombre;
                                    $pasajerosArray[$cantiPasTMP][4]= $datosP->Apellidos;
                                    $pasajerosArray[$cantiPasTMP][5]= $datosP->Fecha_Nac;

                                    if(trim($datosP->Tipo)!='INF')
                                    {
                                            $cantiPas++;
                                    }

                                    $cantiPasTMP++;
                            }
                    }
            }


            //echo var_dump($pasajerosArray); exit;


            //declaracion Variables
            $var_openConex='false';
            $var_getConsultaPRG='false';
            $statusRut='false';


            //Conexion BD
            $var_openConex= $private_functions->openConexion($_SESSION["WS_sess_server_name_conex"], 
                                                                                                            $_SESSION["WS_sess_user_name_conex"], 
                                                                                                            $_SESSION["WS_sess_password_conex"], 
                                                                                                            $_SESSION["WS_sess_bd_conex"]);





            if($var_openConex==false)
            {
                    $functions->getError('1');
            }
            else
            {


                    //Verifica User
                    $var_obtieneUser= $private_functions->getUsuarios($usuarioRQ);
                    if(trim($var_obtieneUser)=="false")
                    {
                            throw new SoapFault("2", "Login Usuario", null, "Usuario o Password son incorrectos"); 
                            exit;
                    }
                    else
                    {
                            foreach($var_obtieneUser as $columUser):
                                    $varUsuario		= trim($columUser["clave"]);
                                    $varPass		= trim($columUser["pasword"]);	
                                    $varIdAgen		= trim($columUser["id_agen"]);	

                                    $varMail		= trim($columUser["email_opera"]);	
                            endforeach;

                            if($usuarioRQ==$varUsuario && $passwordRQ==$varPass)
                            {

                                    //EJECUTANDO STORED PROCEDURE
                                    $var_obtienePERFIL= $private_functions->getPERFIL($usuarioRQ, 'FILEWEB');
                                    foreach($var_obtienePERFIL as $columPERFIL):
                                            $varAcceso		= trim($columPERFIL["acceso"]);
                                    endforeach;

                                    if($varAcceso!= "S")
                                    {
                                            throw new SoapFault("Login Usuario", null, "Usuario NO puede crear reservas"); 
                                            exit;	
                                    }
                                    else
                                    {

                                            $var_getConsultaPRG= $private_functions->getConsultaPRG($usuarioRQ, $KEY_OPCION_RQ);
                                            if(trim($var_getConsultaPRG)!="false")
                                            {
                                                    $ordenPAX='';
                                                    $cntINF=0;
                                                    foreach($var_getConsultaPRG as $columConsultaPRG):


                                                            if(trim($columConsultaPRG["ADL_1"])>0)
                                                            {
                                                                    $adultos[]	= trim($columConsultaPRG["ADL_1"]);
                                                                    for($o=0; $o<($columConsultaPRG["ADL_1"]*1); $o++)
                                                                            $ordenPAX[]='ADL';
                                                            }
                                                            if(trim($columConsultaPRG["CHILD_1_1"])>0)
                                                            {
                                                                    $child_1[]	= trim($columConsultaPRG["CHILD_1_1"]);
                                                                    $ordenPAX[]='CHD';
                                                            }
                                                            if(trim($columConsultaPRG["CHILD_2_1"])>0)
                                                            {
                                                                    $child_2[]	= trim($columConsultaPRG["CHILD_2_1"]);
                                                                    $ordenPAX[]='CHD';
                                                            }
                                                            if(trim($columConsultaPRG["INF_1"])>0)
                                                            {
                                                                    $cntINF=($cntINF+($columConsultaPRG["INF_1"]*1));
                                                                    for($o=0; $o<($columConsultaPRG["INF_1"]*1); $o++)
                                                                            $ordenPAX[]='INF';
                                                            }



                                                            if(trim($columConsultaPRG["ADL_2"])>0)	
                                                            {
                                                                    $adultos[]	= trim($columConsultaPRG["ADL_2"]);
                                                                    for($o=0; $o<($columConsultaPRG["ADL_1"]*1); $o++)
                                                                            $ordenPAX[]='ADL';
                                                            }
                                                            if(trim($columConsultaPRG["CHILD_1_2"])>0)
                                                            {
                                                                    $child_1[]	= trim($columConsultaPRG["CHILD_1_2"]);
                                                                    $ordenPAX[]='CHD';
                                                            }
                                                            if(trim($columConsultaPRG["CHILD_2_2"])>0)
                                                            {
                                                                    $child_2[]	= trim($columConsultaPRG["CHILD_2_2"]);
                                                                    $ordenPAX[]='CHD';
                                                            }
                                                            if(trim($columConsultaPRG["INF_2"])>0)
                                                            {
                                                                    $cntINF=($cntINF+($columConsultaPRG["INF_2"]*1));
                                                                    for($o=0; $o<($columConsultaPRG["INF_2"]*1); $o++)
                                                                            $ordenPAX[]='INF';
                                                            }



                                                            if(trim($columConsultaPRG["ADL_3"])>0)
                                                            {
                                                                    $adultos[]	= trim($columConsultaPRG["ADL_3"]);
                                                                    for($o=0; $o<($columConsultaPRG["ADL_1"]*1); $o++)
                                                                            $ordenPAX[]='ADL';
                                                            }
                                                            if(trim($columConsultaPRG["CHILD_1_3"])>0)
                                                            {
                                                                    $child_1[]	= trim($columConsultaPRG["CHILD_1_3"]);
                                                                    $ordenPAX[]='CHD';
                                                            }
                                                            if(trim($columConsultaPRG["CHILD_2_3"])>0)
                                                            {
                                                                    $child_2[]	= trim($columConsultaPRG["CHILD_2_3"]);
                                                                    $ordenPAX[]='CHD';
                                                            }
                                                            if(trim($columConsultaPRG["INF_3"])>0)
                                                            {
                                                                    $cntINF=($cntINF+($columConsultaPRG["INF_3"]*1));
                                                                    for($o=0; $o<($columConsultaPRG["INF_3"]*1); $o++)
                                                                            $ordenPAX[]='INF';
                                                            }



                                                            $getCons_fechaIn		= trim($columConsultaPRG["fechaIn_consulta"]);
                                                            $getCons_fechaOut		= trim($columConsultaPRG["fechaOut_consulta"]);
                                                            $getCons_habitaciones	= trim($columConsultaPRG["habitaciones_consulta"]);

                                                            $getCons_idPRG			= trim($columConsultaPRG["idPRG"]);
                                                            $getCons_record_c		= trim($columConsultaPRG["record_c"]);
                                                            $getCons_clave			= trim($columConsultaPRG["clave"]);

                                                            $getCons_vHab[0]		= trim($columConsultaPRG["vHab_1"]);
                                                            $getCons_vHab[1]		= trim($columConsultaPRG["vHab_2"]);
                                                            $getCons_vHab[2]		= trim($columConsultaPRG["vHab_3"]);

                                                            $getCons_tipoHab[0]		= trim($columConsultaPRG["tipoHab_1"]);
                                                            $getCons_tipoHab[1]		= trim($columConsultaPRG["tipoHab_2"]);
                                                            $getCons_tipoHab[2]		= trim($columConsultaPRG["tipoHab_3"]);


                                                            for($z=0; $z<=4; $z++)
                                                            {
                                                                    $getCons_Hoteles[$z][0]	= trim($columConsultaPRG["codHotel_".($z+1)]);
                                                                    $getCons_Hoteles[$z][1]	= trim($columConsultaPRG["fechaIn_".($z+1)]);
                                                                    $getCons_Hoteles[$z][2]	= trim($columConsultaPRG["noches_".($z+1)]);
                                                                    $getCons_Hoteles[$z][3]	= trim($columConsultaPRG["codTipoHabitacion_".($z+1)]);
                                                                    $getCons_Hoteles[$z][4]	= trim($columConsultaPRG["codPlanAlimenticio_".($z+1)]);
                                                                    $getCons_Hoteles[$z][5]	= trim($columConsultaPRG["convenio_".($z+1)]);
                                                            }


                                                            $varFechaCons	= trim($columConsultaPRG["fecha_consulta"]);
                                                            $varHoraCons	= trim($columConsultaPRG["hora_consulta"]);


                                                            $varFechaDesde	= trim($columConsultaPRG["desde"]);
                                                    endforeach;


                                                    //echo var_dump($ordenPAX); exit;


                                                    $fechaConsulta= strtotime($varFechaCons.' '.$varHoraCons);
                                                    $fechaReserva= strtotime(date('Y-m-d H:i'));
                                                    $totalMinutos= (($fechaReserva-$fechaConsulta)/60);
                                                    $minutos=15;
                                                    if($totalMinutos<$minutos)//Validando que aun se encuentre dentro de los minutos asignados
                                                    {

                                                            $totalPAS=((intval($adultos[0])+intval($adultos[1])+intval($adultos[2]))+count($child_1)+count($child_2));
                                                            if($cantiPas==$totalPAS) // Comparando cantidad de pasajeros INGRESADOS con los CONSULTADOS
                                                            {


                                                                    $sqlPasajeros='';
                                                                    $statusError=0;
                                                                    $cntINF_tmp=0;
                                                                    for($i=0; $i<($cantiPas+$cntINF); $i++)
                                                                    {
                                                                            $tipoPAS='';
                                                                            if(trim($pasajerosArray[$i][0])=='') //TIPO de pasajero
                                                                            {
                                                                                    throw new SoapFault("10", "Error Pasajero", null, "Debe ingresar el TIPO de pasajero"); 
                                                                                    exit;
                                                                            }
                                                                            else if(trim($pasajerosArray[$i][0])!='ADL' && trim($pasajerosArray[$i][0])!='CHD' && trim($pasajerosArray[$i][0])!='INF') 
                                                                            {
                                                                                    throw new SoapFault("13", "Error Pasajero", null, 'El TIPO de pasajero debe ser "ADL", "CHD" Ó "INF"'); 
                                                                                    exit;
                                                                            }





                                                                            if(trim($pasajerosArray[$i][1])!='' && trim($pasajerosArray[$i][2])!='') //RUT y PASAPORTE NO vacios
                                                                            {
                                                                                    throw new SoapFault("7", "Error Pasajero", null, 'No puede ingresar RUT y PASAPORTE'); 
                                                                                    exit;
                                                                            }
                                                                            else if(trim($pasajerosArray[$i][1])=='' && trim($pasajerosArray[$i][2])=='') //RUT y PASAPORTE vacios
                                                                            {
                                                                                    throw new SoapFault("8", "Error Pasajero", null, 'RUT o PASAPORTE no pueden ser vacios'); 
                                                                                    exit;
                                                                            }
                                                                            else if(trim($pasajerosArray[$i][2])!='')
                                                                            {
                                                                                    $partesRUT= explode('-', trim($pasajerosArray[$i][2]));
                                                                                    $statusRut= $functions->rut_valido($partesRUT[0], $partesRUT[1]);
                                                                                    if($statusRut==false)
                                                                                    {
                                                                                            throw new SoapFault("9", "Error Pasajero", null, 'El RUT ['.$pasajerosArray[$i][2].'] no es valido'); 
                                                                                            exit;
                                                                                    }
                                                                            }


                                                                            if(trim($pasajerosArray[$i][3])=='') //NOMBRE del pasajero
                                                                            {
                                                                                    throw new SoapFault("11", "Error Pasajero", null, 'Debe ingresar el NOMBRE deL pasajero'); 
                                                                                    exit;
                                                                            }

                                                                            if(trim($pasajerosArray[$i][4])=='') //APELLIDOS del pasajero
                                                                            {
                                                                                    throw new SoapFault("12", "Error Pasajero", null, 'Debe ingresar el APELLIDO deL pasajero'); 
                                                                                    exit;
                                                                            }





                                                                            if(trim($pasajerosArray[$i][0])=='ADL') //TIPO de pasajero
                                                                            {
                                                                                    $cntINF_tmp=0;
                                                                                    $posADL[]=$i;
                                                                                    if($ordenPAX[$i]=='ADL')
                                                                                    {
                                                                                            $tipoPAS='A';
                                                                                            if(trim($pasajerosArray[$i][5])!='') //FECHA NAC
                                                                                            {
                                                                                                    $fechaValida='false';
                                                                                                    $fechaValida= $functions->validaFecha(trim($pasajerosArray[$i][5]));
                                                                                                    if($fechaValida=='false')
                                                                                                    {
                                                                                                            throw new SoapFault("14", "Error Pasajero", null, 'Fecha Nacimiento del ADL no es válida'); 
                                                                                                            exit;
                                                                                                    }
                                                                                                    else
                                                                                                    {
                                                                                                            $fechaENG='';
                                                                                                            $fechaENG=$functions->fechaEng(trim($pasajerosArray[$i][5]), '/', '-');
                                                                                                            $fecha1=$functions->fechaMKTIME($fechaENG);
                                                                                                            $fecha2=$functions->fechaMKTIME(str_replace("/", "-", $varFechaDesde));

                                                                                                            $totalFecha= $fecha2-$fecha1;
                                                                                                            $fechaCalc= round((((($totalFecha/60)/60)/24)));

                                                                                                            if($fechaCalc < 4383)
                                                                                                            {
                                                                                                                    throw new SoapFault("18", "Error Pasajero", null, 'Edad ADL debe ser >= 12 años desde la fecha de salida'); 
                                                                                                                    exit;
                                                                                                            }

                                                                                                    }
                                                                                            }

                                                                                            //SQL PASAJEROS
                                                                                            $sqlPasajeros[]= " '".trim($pasajerosArray[$i][3])." ".trim($pasajerosArray[$i][4])."', '".trim($pasajerosArray[$i][2])."', 
                                                                                                            '".$functions->fechaEng(trim($pasajerosArray[$i][5]),"/","-")."', '".$tipoPAS."', '', '', '' , ";
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                            throw new SoapFault("16", "Error Pasajero", null, 'El orden de los pasajeros no coincide segun lo consultado anteriormente ['.trim($pasajerosArray[$i][0]).']['.$ordenPAX[$i].']['.$i.'].'); 
                                                                                            exit;
                                                                                    }
                                                                            }

                                                                            else


                                                                            if(trim($pasajerosArray[$i][0])=='CHD') //VALIDANDO pasajero CHILD
                                                                            {
                                                                                    $cntINF_tmp=0;
                                                                                    if($ordenPAX[$i]=='CHD')
                                                                                    {
                                                                                            $tipoPAS='C';
                                                                                            if(trim($pasajerosArray[$i][5])=='') //FECHA NAC
                                                                                            {
                                                                                                    throw new SoapFault("13", "Error Pasajero", null, 'Debe ingresar Fecha Nacimiento para el CHD'); 
                                                                                                    exit;
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                    $fechaValida='false';
                                                                                                    $fechaValida= $functions->validaFecha(trim($pasajerosArray[$i][5]));
                                                                                                    if($fechaValida=='false')
                                                                                                    {
                                                                                                            throw new SoapFault("14", "Error Pasajero", null, 'Fecha Nacimiento del CHD no es válida'); 
                                                                                                            exit;
                                                                                                    }
                                                                                                    else
                                                                                                    {
                                                                                                            $fechaENG='';
                                                                                                            $fechaENG=$functions->fechaEng(trim($pasajerosArray[$i][5]), '/', '-');
                                                                                                            $fecha1=$functions->fechaMKTIME($fechaENG);
                                                                                                            $fecha2=$functions->fechaMKTIME(str_replace("/", "-", $varFechaDesde));

                                                                                                            $totalFecha= $fecha2-$fecha1;
                                                                                                            $fechaCalc= round((((($totalFecha/60)/60)/24)));

                                                                                                            if(($fechaCalc >= 4383) || ($fechaCalc < 731))
                                                                                                            {
                                                                                                                    throw new SoapFault("17", "Error Pasajero", null, 'Edad CHD debe ser >= 2 años y menor que 12 años desde la fecha de salida ['.$varFechaDesde.']['.$fechaCalc.']'); 
                                                                                                                    exit;
                                                                                                            }
                                                                                                    }



                                                                                            }


                                                                                            //SQL PASAJEROS
                                                                                            $sqlPasajeros[]= " '".trim($pasajerosArray[$i][3])." ".trim($pasajerosArray[$i][4])."', '".trim($pasajerosArray[$i][2])."', 
                                                                                                            '".$functions->fechaEng(trim($pasajerosArray[$i][5]),"/","-")."', '".$tipoPAS."', '', '', '' , ";
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                            throw new SoapFault("16", "Error Pasajero", null, 'El orden de los pasajeros no coincide segun lo consultado anteriormente ['.trim($pasajerosArray[$i][0]).']['.$ordenPAX[$i].']['.$i.'].'); 
                                                                                    exit;
                                                                                    }
                                                                            }

                                                                            else

                                                                            if(trim($pasajerosArray[$i][0])=='INF') //VALIDANDO pasajero CHILD
                                                                            {
                                                                                    if($ordenPAX[$i]=='INF')
                                                                                    {
                                                                                            $tipoPAS='A';
                                                                                            if(trim($pasajerosArray[$i][5])=='') //FECHA NAC
                                                                                            {
                                                                                                    throw new SoapFault("13", "Error Pasajero", null, 'Debe ingresar Fecha Nacimiento para el INF'); 
                                                                                                    exit;
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                    $fechaValida='false';
                                                                                                    $fechaValida= $functions->validaFecha(trim($pasajerosArray[$i][5]));
                                                                                                    if($fechaValida=='false')
                                                                                                    {
                                                                                                            throw new SoapFault("14", "Error Pasajero", null, 'Fecha Nacimiento del INF no es válida'); 
                                                                                                            exit;
                                                                                                    }
                                                                                                    else
                                                                                                    {
                                                                                                            $fechaENG='';
                                                                                                            $fechaENG=$functions->fechaEng(trim($pasajerosArray[$i][5]), '/', '-');
                                                                                                            $fecha1=$functions->fechaMKTIME($fechaENG);
                                                                                                            $fecha2=$functions->fechaMKTIME(str_replace("/", "-", $varFechaDesde));

                                                                                                            $totalFecha= $fecha2-$fecha1;
                                                                                                            $fechaCalc= round((((($totalFecha/60)/60)/24)));

                                                                                                            if(($fechaCalc >= 731))
                                                                                                            {
                                                                                                                    throw new SoapFault("17", "Error Pasajero", null, 'Edad INF debe ser MENOR a 2 años desde la fecha de salida'); 
                                                                                                                    exit;
                                                                                                            }
                                                                                                    }


                                                                                                            //echo var_dump($posADL); exit;

                                                                                                    //SQL PASAJEROS
                                                                                                    $sqlPasajeros[$posADL[$cntINF_tmp]]= " '".trim($pasajerosArray[$posADL[$cntINF_tmp]][3])." ".trim($pasajerosArray[$posADL[$cntINF_tmp]][4])."', 
                                                                                                                                            '".trim($pasajerosArray[$posADL[$cntINF_tmp]][2])."', 
                                                                                                                                            '".$functions->fechaEng(trim($pasajerosArray[$posADL[$cntINF_tmp]][5]),"/","-")."', 
                                                                                                                                            '".$tipoPAS."', 
                                                                                                                                            '".trim($pasajerosArray[$i][3])." ".trim($pasajerosArray[$i][4])."', 
                                                                                                                                            '".trim($pasajerosArray[$i][2])."', 
                                                                                                                                            '".$functions->fechaEng(trim($pasajerosArray[$i][5]),"/","-")."' , ";
                                                                                                    $cntINF_tmp++;


                                                                                                    if(trim($pasajerosArray[($i+1)][0])!='INF')
                                                                                                    {
                                                                                                            unset($posADL);
                                                                                                    }

                                                                                                    /*

                                                                                                    */

                                                                                            }
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                            throw new SoapFault("16", "Error Pasajero", null, 'El orden de los pasajeros no coincide segun lo consultado anteriormente ['.trim($pasajerosArray[$i][0]).']['.$ordenPAX[$i].']['.$i.'].'); 
                                                                                            exit;
                                                                                    }
                                                                            }









                                                                    }




                                                                    //SQL LLenando pasajeros faltantes
                                                                    for($y=$cantiPas; $y<10; $y++)
                                                                    {
                                                                            $sqlPasajeros[]= " '', '', '', '', '', '', '' , ";
                                                                    }



                                                                    if($statusError==0)
                                                                    {

                                                                            $var_obtieneDatosCons= $private_functions->getHeaderConsultaPRG($usuarioRQ, $getCons_idPRG);
                                                                            if(trim($var_obtieneDatosCons)=='false')
                                                                            {
                                                                                    throw new SoapFault("15", "Error Interno", null, 'Contactese con el administrador'); 
                                                                                    exit;
                                                                            }
                                                                            else
                                                                            {
                                                                                    foreach($var_obtieneDatosCons as $columCons):
                                                                                            $varDC_codigoPRG = trim($columCons["codigoPRG"]);
                                                                                    endforeach;

                                                                                    for($x=0; $x<=2; $x++)
                                                                                    {
                                                                                            if(!empty($adultos[$x]))
                                                                                            {
                                                                                                    $cntADL[$x]=$adultos[$x];
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                    $cntADL[$x]=0;
                                                                                            }

                                                                                            if(!empty($child_1[$x]))
                                                                                            {
                                                                                                    $cntCHD[$x]=1;
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                    $cntCHD[$x]=0;
                                                                                            }

                                                                                            if(!empty($child_1[$x]) && !empty($child_2[$x]))
                                                                                            {
                                                                                                    $cntCHD[$x]=2;
                                                                                            }
                                                                                    }





                                                                                    //EJECUTAR SP
                                                                                    $sqlEXEC="exec WEB_ORIS_CREA_FILE '".$varIdAgen."', '".$usuarioRQ."', '".$getCons_fechaIn."', '".$varDC_codigoPRG."', '".$getCons_record_c."', ";

                                                                                    //HOTELES
                                                                                    for($z=0; $z<=4; $z++)
                                                                                    {
                                                                                            $sqlEXEC.= " '".$getCons_Hoteles[$z][0]."', '".trim($getCons_Hoteles[$z][1])."', 
                                                                                                                    '".$getCons_Hoteles[$z][2]."', '".$getCons_Hoteles[$z][3]."', '".$getCons_Hoteles[$z][4]."', '".$getCons_Hoteles[$z][5]."' , ";
                                                                                    }


                                                                                    $sqlEXEC.=" '".$getCons_habitaciones."', '".$cntADL[0]."', '".$cntCHD[0]."', '".$cntADL[1]."', 
                                                                                                            '".$cntCHD[1]."', '".$cntADL[2]."', '".$cntCHD[2]."' , 'E', 'D' , ";




                                                                                    //PASAJEROS
                                                                                    for($y=0; $y<10; $y++)
                                                                                    {
                                                                                            $sqlEXEC.=" ".$sqlPasajeros[$y]." ";
                                                                                    }





                                                                                    //TOTAL VENTA
                                                                                    $xtotventa= (($getCons_vHab[0]*1)+($getCons_vHab[1]*1)+($getCons_vHab[2]*1));


                                                                                    $sqlEXEC.=" '".$getCons_tipoHab[0]."', '".$getCons_tipoHab[1]."', '".$getCons_tipoHab[2]."', '".$getCons_clave."', 'comentario', '".$xtotventa."' ";


                                                                                    //echo $sqlEXEC; exit;

                                                                                    $resultadoSP_CREA= $private_functions->consulta($sqlEXEC);
                                                                                    $resultadoArray_CREA= $private_functions->fetch_array($resultadoSP_CREA);
                                                                                    foreach ($resultadoArray_CREA as $columnasSP_CREA):
                                                                                                    $RS_CODIGO= trim($columnasSP_CREA['CODIGO']);
                                                                                                    $RS_MENSAJE= trim($columnasSP_CREA['MENSAJE']);
                                                                                                    $RS_FILE= trim($columnasSP_CREA['FILE']);
                                                                                                    break;
                                                                                    endforeach;


                                                                                    if($RS_CODIGO==1)
                                                                                    {
                                                                                            $mailNombrePax= trim($pasajerosArray[0][3])." ".trim($pasajerosArray[0][4]);
                                                                                            $mailAgencia=$varIdAgen;
                                                                                            $mailFechaViaje=$varFechaDesde;
                                                                                            $mailNPasajeros=$cantiPas;
                                                                                            $mailTotalVenta=$xtotventa;
                                                                                            $mailFile=$RS_FILE;

                                                                                            //Preparar Correo electrónico
                                                                                            include('envioMail.php');

                                                                                            $xmlResponse= array(
                                                                                                                            "TOTAL_VENTA" => $xtotventa,
                                                                                                                            "COMAG" => $RS_MENSAJE,
                                                                                                                            "FILE" => $RS_FILE
                                                                                                                            );
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                            throw new SoapFault("7", "Error Reserva", null, '[SP] '.$RS_MENSAJE); 
                                                                                            exit;
                                                                                    }
                                                                            }

                                                                    }

                                                            }
                                                            else
                                                            {
                                                                    throw new SoapFault("6", "Error Reserva", null, 'Cantidad de pasajeros a reservar NO coincide con la cantidad consultada anteriormente de ['.$totalPAS.'] Pasajeros'); 
                                                                    exit;
                                                            }

                                                    }
                                                    else
                                                    {
                                                            throw new SoapFault("5", "Error Reserva", null, 'Ya expiro el tiempo limite de '.$minutos.' minutos para poder reservar ó la KEY_OPCION[ '.$KEY_OPCION_RQ.' ] no es correcta.'); 
                                                            exit;
                                                    }
                                                    //END: $fechaConsulta-$fechaReserva

                                            }
                                            else
                                            {
                                                    throw new SoapFault("3", "Error Reserva", null, 'Para poder reservar, primero debe consultar programas'); 
                                                    exit;
                                            }


                                    }// END EJECUTANDO STORED PROCEDURE


                            }
                            else
                            {
                                    throw new SoapFault("2", "Error Login", null, 'Usuario o Password son incorrectos'); 
                                    exit;
                            }


                            return $xmlResponse;
                    }//End: obtieneUser

            }//End: $openConex

            $private_functions->closeConexion();
            exit();
    }





    public function obtieneCiudadesRQ($args)
    {
        /* 	
        Rescatando Object enviado desde el WSDL hacia $args = (array)$args;
        $args["Credenciales"]->Usuario 
        */   
        $args = (array)$args;

        
        $usuarioRQ= trim($args["Credenciales"]->usuario);
        $passwordRQ= trim($args["Credenciales"]->password);
        $OPCION_RQ= trim($args["Parametros"]->OPCION);

        //echo $usuarioRQ.' - '.$passwordRQ.' - '.$OPCION_RQ; exit;
        
        
        $usuarios= $this->loadModel('usuarios');


        
        //Verifica User
        $var_obtieneUser= $usuarios->getUsuario($usuarioRQ);
        if($var_obtieneUser==false)
        {
            throw new SoapFault("Login Usuario", null, "Usuario o Password son Incorrectos.");
        }
        else
        {
                foreach($var_obtieneUser as $columUser):
                        $varUsuario		= trim($columUser["clave"]);
                        $varPass		= trim($columUser["pasword"]);	
                        $varIdAgen		= trim($columUser["id_agen"]);
                endforeach;

                if($usuarioRQ==$varUsuario && $passwordRQ==$varPass)
                {

                        if($OPCION_RQ==1 || $OPCION_RQ==2)
                        {
                                $var_obtieneCiudades= $private_functions->getCiudades($OPCION_RQ);
                                if(trim($var_obtieneCiudades)=="false")
                                {
                                        throw new SoapFault("Sin registros", null, "No se encontraron ciudades.");
                                }
                                else
                                {
                                        foreach($var_obtieneCiudades as $columCiudades):
                                                $varCodigo		= trim($columCiudades["codigo"]);
                                                $varCiudad		= trim($columCiudades["nombre"]);	

                                                $xmlCiudades[]= array(
                                                                        "Codigo" =>  mb_convert_encoding($varCodigo, 'UTF-8', 'ISO-8895-1'),
                                                                        "Nombre" => mb_convert_encoding($varCiudad, 'UTF-8', 'ISO-8895-1')
                                                                        );
                                        endforeach;


                                        $xmlResponse= array("Ciudad" => $xmlCiudades);
                                }

                        }
                        else
                        {
                                throw new SoapFault("1", "Error consulta", null, "La opcion a ingresar debe ser [1] ó [2]");
                        }




                }
                else
                {
                        throw new SoapFault("Login Usuario", null, "Usuario o Password son Incorrectos.");
                }


                return $xmlResponse;
        }
        
        
        exit;
    }
	
        
}
?>