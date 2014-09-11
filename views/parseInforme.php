<?php

/* 
 * Proyecto : PanamericanaWS
 * Autor    : Tsyacom Ltda.
 * Fecha    : Martes, 19 de agosto de 2014
 */

$datosPackages= $LM_reserva->getPackages($MC_CodigoPrograma);
$datosDetFile= $LM_reserva->getDetFile($mC_TC_file);
$datosDetBloq= $LM_reserva->getDetBloq($MC_CodigoBloqueo, $mC_TC_file);
$datosBloq= $LM_reserva->getBloqeos($MC_CodigoBloqueo);

//CARGAR LA PLANTILLA DEL CORREO
$mC_HTML=file_get_contents( ROOT . 'views' . DS . 'detalle_informe.html');

$nodosHTML= array();
$nodosHTML["fecha_act"]= date("d F Y");
$nodosHTML["file"]= $mC_TC_file;
$nodosHTML["agencia"]= trim($datosFile[0]['agencia']);
$nodosHTML["nombre_user"]= trim($var_getUser[0]['nombre']);

$nodosHTML["nombre_pax"]= trim($datosFile[0]['nompax']);
$nodosHTML["num_pax"]= trim($datosFile[0]['npax']);
$nodosHTML["fecha_viaje"]= trim($datosFile[0]['f_viaje']);

$nodosHTML["nombre_prog"]= $datosPackages[0]['nombre'];


//INCLUYE 
$mC_incluye='';
if($datosDetFile!=false)
{
    foreach($datosDetFile as $columnDF):
        $mC_totPax=(intval($columnDF["pax_s"]) + intval($columnDF["pax_d"]) + 
                    intval($columnDF["pax_t"]) + intval($columnDF["pax_q"]) + 
                    intval($columnDF["pax_a"]) + intval($columnDF["pax_i"]) + 
                    intval($columnDF["pax_c"]) + intval($columnDF["pax_ca"]) + 
                    intval($columnDF["pax_c2"]));

        $mC_incluye.='<tr>
<td width="80%" class="Base"><strong>&middot;</strong>&nbsp; '.$mC_totPax.' Pax '.trim($columnDF["nombre"]).'<strong></strong></td>
<td width="10%" class="Base">';

        if(trim($columnDF["in_"]) != "01/01/1900")
        {
            $mC_incluye.= trim($columnDF["in_"]); 
        }

        $mC_incluye.='</td>
<td width="10%" class="Base">';

        if(trim($columnDF["out"]) != "01/01/1900")
        {
            $mC_incluye.= trim($columnDF["out"]);
        }

        $mC_incluye.='</td></tr>';
    endforeach;
}

$nodosHTML["incluye"]=$mC_incluye;

$mc_totVenta=trim($datosFile[0]['totventa']);
$mc_ajuste=trim($datosFile[0]['ajuste']);

if(trim($datosFile[0]['moneda']) == "D")
{
    $nodosHTML["texto_moneda"]='D&oacute;lares Americanos (USD)';
    $nodosHTML["valor_total"]=number_format($mc_totVenta, 2, ',', '.').' + '.number_format($mc_ajuste, 2, ',', '.');
    $nodosHTML["tipo_cambio"]='<tr>
                                <td width="25%" bgcolor="#E6E6E6">
                                    <strong>&nbsp;Tipo de cambio al d&iacute;a de hoy </strong>
                                </td>
                                <td width="2%">:</td>
                                <td width="75%">
                                        540 (Consultar al momento del pago)
                                </td>
                            </tr>';
}
elseif(trim($datosFile[0]['moneda']) == "P")
{
    $nodosHTML["texto_moneda"]='Pesos Chilenos ($)';
    $nodosHTML["valor_total"]=number_format($mc_totVenta, 0, '', '.').' + '.number_format($mc_ajuste, 0, '', '.');
    $nodosHTML["tipo_cambio"]='';
}


$nodosHTML["comag"]=trim($datosFile[0]['comag']);

if(trim($datosFile[0]['tcomi'])=='1')
{
    $nodosHTML["tcomi"]=' + I.V.A.';
}
else if(trim($datosFile[0]['tcomi'])=='1')
{
    $nodosHTML["tcomi"]=' I.V.A. Incluido';
}



if($datosDetBloq!=false)
{
    $mC_detBloq='';
    $cntDetBloq=1;
    foreach($datosDetBloq as $columnDB):
        $mC_detBloq.='<tr>
                <td>'.$cntDetBloq.'</td>
                <td>';

        if(trim($columnDB["tipo_pax"]) == 'A')
        {
            $mC_detBloq.="Adulto";
        }
        else if($columnDB["tipo_pax"] == 'C')
        {
            $mC_detBloq.="Child";
        }

        $mC_detBloq.=	'</td>
                <td>'.str_replace('/', ' ', trim($columnDB["nompax"])).'</td>
                <td>'.trim($columnDB["rut"]).'</td>
                <td>';

        if(trim($columnDB["fchild"]) == "01/01/1900") 
        {
            $mC_detBloq.=''; 
        }
        else 
        {
            $mC_detBloq.=trim($columnDB["fchild"]);
        }

        $mC_detBloq.='</td>
                <td>'.trim($columnDB["ninfant"]).'</td>
                <td>'.trim($columnDB["rut_inf"]).'</td>
                <td>';

        if(trim($columnDB["finfant"]) == "01/01/1900")
        {
            $mC_detBloq.=''; 
        }
        else 
        {
            $mC_detBloq.=$columnDB["finfant"];
        }
        $mC_detBloq.='</td>
        </tr>';

        ++$cntDetBloq;
    endforeach;
    $nodosHTML["detalle_pasajeros"]=$mC_detBloq;
}



$nodosHTML["itinerario_vuelo"]=str_replace("\n", "<br>", trim($datosBloq[0]['notas']));

foreach($arrayHTML as $nombreNodo=>$valorNodo):
    $mC_HTML= str_replace('{'.$nombreNodo.'}', $valorNodo, $mC_HTML);
endforeach;
?>