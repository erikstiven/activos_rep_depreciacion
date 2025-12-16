<?php

require("_Ajax.comun.php"); // No modificar esta linea
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  // S E R V I D O R   A J A X //
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */


function genera_cabecera_formulario($sAccion = 'nuevo', $aForm = '')
{
	//Definiciones
	global $DSN_Ifx, $DSN;

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oIfx1 = new Dbo();
	$oIfx1->DSN = $DSN_Ifx;
	$oIfx1->Conectar();

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$fu = new Formulario;
	$fu->DSN = $DSN;

	$ifu = new Formulario;
	$ifu->DSN = $DSN_Ifx;

	$oReturn = new xajaxResponse();

	//variables de sesion
	$idempresa = $_SESSION['U_EMPRESA'];

	//variables del formulario
	$empresa = $aForm['empresa'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}


	switch ($sAccion) {
		case 'nuevo':
			$ifu->AgregarCampoListaSQL('empresa', 'Empresa|left', "select empr_cod_empr, empr_nom_empr
															from saeempr
															order by empr_nom_empr", true, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('empresa', 'f_filtro_sucursal()', true, 150, 150);
			$ifu->AgregarCampoListaSQL('sucursal', 'Sucursal|left', '', false, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('sucursal', 'f_filtro_anio_i(); f_filtro_anio_f(); f_filtro_grupo()', true, 150, 150);
			$ifu->AgregarCampoListaSQL('anio', 'A&ntildeo Desde|left', "", true, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('anio', 'f_filtro_mes()', true, 150, 150);
			$ifu->AgregarCampoListaSQL('anio_fin', 'A&ntildeo Hasta|left', "", true, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('anio_fin', 'f_filtro_mes_fin()', true, 150, 150);
			$ifu->AgregarCampoListaSQL('mes', 'Mes Desde|left', "", true, 150, 150);
			$ifu->AgregarCampoListaSQL('mes_fin', 'Mes Hasta|left', "", true, 150, 150);
			$ifu->AgregarCampoListaSQL('cod_grupo', 'Grupo|left', "", false, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('cod_grupo', 'f_filtro_subgrupo()');
			$ifu->AgregarCampoListaSQL('cod_subgrupo', "Subgrupo|left", "", false, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('cod_subgrupo', 'f_filtro_activos();f_filtro_activos1()');
			$ifu->AgregarCampoListaSQL('cod_activo_desde', 'Activo Desde|left', "", false, 150, 150);
			$ifu->AgregarCampoListaSQL('cod_activo_hasta', 'Activo Hasta|left', "", false, 150, 150);
	}

	$table_op .= '<table class="table table-striped table-condensed" style="width: 70%; margin-bottom: 0px;" >
		<tr> 
                    <td colspan="8" align="center" class="bg-primary">REPORTE DEPRECIACION DE ACTIVOS</td>
		</tr>
                <tr>
                    <td colspan = "8">    
                        <div class="btn-group">
						<div class="btn btn-primary btn-sm" onclick="genera_cabecera_formulario();" >
                                    <span class="glyphicon glyphicon-file"></span>
                                    Nuevo
                            </div>
                            <div class="btn btn-primary btn-sm" onclick="generar();" id = "generar">
                                    <span class="glyphicon glyphicon-search"></span>
                                    Consultar
                            </div>
                            <div class="btn btn-primary btn-sm" onclick="f_exportar();" id = "exportar">
                                    <span class="glyphicon glyphicon-cog"></span>
                                    Excel
                            </div>							
                        </div>
                    </td>                   
                </tr>
                <tr class="msgFrm">
                    <td colspan="8" align="center">Los campos con * son de ingreso obligatorio</td>
                </tr>
				<tr>
                    <td>' . $ifu->ObjetoHtmlLBL('empresa') . '</td>
                    <td>' . $ifu->ObjetoHtml('empresa') . '</td>
                    <td>' . $ifu->ObjetoHtmlLBL('sucursal') . '</td>
                    <td>' . $ifu->ObjetoHtml('sucursal') . '</td>
					<td>
						<label for="tipo">Activos Revalorizados</label>
					</td>	
					<td>
						<input type="checkbox" name="tipo" id="tipo" value="S">							
					</td>
					<td>
						<label for="tipo">Detallado</label>
					</td>	
					<td>
						<input type="checkbox" name="detallado" id="detallado" value="S">							
					</td>
					<td> </td>						
					<td> </td>						
				</tr>
				<tr>
                    <td>' . $ifu->ObjetoHtmlLBL('anio') . '</td>
                    <td>' . $ifu->ObjetoHtml('anio') . '</td>
                    <td>' . $ifu->ObjetoHtmlLBL('mes') . '</td>
                    <td>' . $ifu->ObjetoHtml('mes') . '</td>
                    <td>' . $ifu->ObjetoHtmlLBL('anio_fin') . '</td>
                    <td>' . $ifu->ObjetoHtml('anio_fin') . '</td>
                    <td>' . $ifu->ObjetoHtmlLBL('mes_fin') . '</td>
                    <td>' . $ifu->ObjetoHtml('mes_fin') . '</td>
				</tr>

				 <tr>
                    <td>' . $ifu->ObjetoHtmlLBL('cod_grupo') . '</td>
                    <td>' . $ifu->ObjetoHtml('cod_grupo') . '</td>
                    <td>' . $ifu->ObjetoHtmlLBL('cod_subgrupo') . '</td>
                    <td>' . $ifu->ObjetoHtml('cod_subgrupo') . '</td>
					<td>' . $ifu->ObjetoHtmlLBL('cod_activo_desde') . '</td>
                    <td>' . $ifu->ObjetoHtml('cod_activo_desde') . '</td>
					<td>' . $ifu->ObjetoHtmlLBL('cod_activo_hasta') . '</td>
                    <td>' . $ifu->ObjetoHtml('cod_activo_hasta') . '</td>					
				</tr>
                </table>				
				<br>
				<div id = "reporte"> </div>';
	$table_op .= '</fieldset>';
	$oReturn->assign("divFormularioReportesDepr", "innerHTML", $table_op);

	return $oReturn;
}

function f_filtro_sucursal($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();

	//variables formulario
	$empresa = $aForm['empresa'];

	// DATOS EMPRESA
	$sql = "select sucu_cod_sucu, sucu_nom_sucu
			from saesucu
			where sucu_cod_empr = '$empresa'			
			order by sucu_nom_sucu";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_sucursal();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_sucursal(' . $i++ . ',\'' . $oIfx->f('sucu_cod_sucu') . '\', \'' . $oIfx->f('sucu_nom_sucu') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('sucursal', 'value', $data);
	return $oReturn;
}

function f_filtro_anio_i($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();
	$idempresa = $_SESSION['U_EMPRESA'];
	//variables formulario
	$empresa = $aForm['empresa'];
	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	// DATOS EMPRESA
	$sql = "select ejer_fec_inil, DATE_PART('year', ejer_fec_inil) as anio_i 
			from saeejer 
			where ejer_cod_empr = $empresa
			order by anio_i desc";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_anio_i();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_anio_i(' . $i++ . ',\'' . $oIfx->f('anio_i') . '\',\'' . $oIfx->f('anio_i') . '\')'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('anio', 'value', $data);
	return $oReturn;
}

function f_filtro_anio_f($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();
	$idempresa = $_SESSION['U_EMPRESA'];
	//variables formulario
	$empresa = $aForm['empresa'];
	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	// DATOS EMPRESA
	$sql = "select ejer_fec_inil, DATE_PART('year', ejer_fec_inil) as anio 
			from saeejer 
			where ejer_cod_empr = $empresa
			order by anio desc";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_anio_f();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_anio_f(' . $i++ . ',\'' . $oIfx->f('anio') . '\',\'' . $oIfx->f('anio') . '\')'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('anio_fin', 'value', $data);
	return $oReturn;
}

function f_filtro_grupo($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();
	$idempresa = $_SESSION['U_EMPRESA'];
	//variables formulario
	$empresa = $aForm['empresa'];
	$sucursal = $aForm['sucursal'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	if (empty($sucursal)) {
		$sucursal = $idsucursal;
	}

	// DATOS DEL GRUPO POR EMPRESA
	$sql = "select gact_cod_gact, gact_des_gact 
			 from saegact 
			 where gact_cod_empr = '$empresa'                                                                  
			 order by gact_des_gact";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_grupo();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_grupo(' . $i++ . ',\'' . $oIfx->f('gact_cod_gact') . '\', \'' . $oIfx->f('gact_des_gact') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}

	$oReturn->assign('cod_grupo', 'value', $data);
	$oReturn->assign('cod_activo_desde', 'value', null);
	$oReturn->assign('cod_activo_hasta', 'value', null);

	return $oReturn;
}

function f_filtro_subgrupo($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();
	$idempresa = $_SESSION['U_EMPRESA'];

	//variables formulario
	$empresa = $aForm['empresa'];
	$codigoGrupo = $aForm['cod_grupo'];
	if (empty($empresa)) {
		$empresa = $idempresa;
	}

	// DATOS DEL ACTIVO
	$sql = "select sgac_cod_sgac, sgac_des_sgac 
			 from saesgac where sgac_cod_empr = '$empresa'                                                                  
			 and gact_cod_gact = '$codigoGrupo'
			 order by sgac_des_sgac";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_subgrupo();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_subgrupo(' . $i++ . ',\'' . $oIfx->f('sgac_cod_sgac') . '\', \'' . $oIfx->f('sgac_des_sgac') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}

	$oReturn->assign('cod_subgrupo', 'value', $data);

	return $oReturn;
}

function f_filtro_mes($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();

	//variables formulario
	$idempresa =  $_SESSION['U_EMPRESA'];
	$anio 	   =  $aForm['anio'];
	$empresa   =  $aForm['empresa'];
	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	//echo $anio; exit;
	// DATOS DEL ACTIVO
	$sql = "select prdo_num_prdo, prdo_nom_prdo
			from saeprdo
			where prdo_cod_empr = '$empresa'
			and prdo_cod_ejer  = (select ejer_cod_ejer 
									from saeejer 
									where ejer_cod_empr = '$empresa' 
									and DATE_PART('year', ejer_fec_inil) = $anio)
			order by prdo_num_prdo";
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_mes();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_mes(' . $i++ . ',\'' . $oIfx->f('prdo_num_prdo') . '\', \'' . $oIfx->f('prdo_nom_prdo') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}

	$oReturn->assign('mes', 'value', $data);

	return $oReturn;
}

function f_filtro_mes_fin($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();

	//variables formulario
	$anio = $aForm['anio_fin'];
	$empresa = $_SESSION['U_EMPRESA'];
	// DATOS DEL ACTIVO
	$sql = "select prdo_num_prdo, prdo_nom_prdo
			from saeprdo
			where prdo_cod_empr = '$empresa'
			and prdo_cod_ejer  = (select ejer_cod_ejer 
									from saeejer 
									where ejer_cod_empr = '$empresa' 
									and DATE_PART('year', ejer_fec_inil) = $anio)
			order by prdo_num_prdo";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_mes_fin();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_mes_fin(' . $i++ . ',\'' . $oIfx->f('prdo_num_prdo') . '\', \'' . $oIfx->f('prdo_nom_prdo') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}

	$oReturn->assign('mes_fin', 'value', $data);

	return $oReturn;
}

function f_filtro_activos($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();

	//variables formulario   
	$idempresa = $_SESSION['U_EMPRESA'];
	$idsucursal = $_SESSION['U_SUCURSAL'];
	$subgrupo = $aForm['cod_subgrupo'];
	$empresa = $aForm['empresa'];
	$sucursal = $aForm['sucursal'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	if (empty($sucursal)) {
		$sucursal = $idsucursal;
	}

	// DATOS DEL ACTIVO
	$sql = "select act_cod_act, act_nom_act
			from saeact
			where act_cod_empr = '$empresa'
			and act_cod_sucu = '$sucursal'
			and sgac_cod_sgac  = '$subgrupo'
			order by act_cod_act";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_activo();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_activo(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_nom_act') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('cod_activo_desde', 'value', $data);
	return $oReturn;
}

function f_filtro_activos1($aForm, $data)
{
	//Definiciones
	global $DSN, $DSN_Ifx;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oReturn = new xajaxResponse();

	//variables formulario
	$subgrupo = $aForm['cod_subgrupo'];
	$empresa = $_SESSION['U_EMPRESA'];
	$sucursal = $_SESSION['U_SUCURSAL'];
	$empresa = $aForm['empresa'];
	$sucursal = $aForm['sucursal'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	if (empty($sucursal)) {
		$sucursal = $idsucursal;
	}

	// DATOS DEL ACTIVO
	$sql = "select act_cod_act, act_nom_act
			from saeact
			where act_cod_empr = '$empresa'
			and act_cod_sucu = '$sucursal'
			and sgac_cod_sgac  = '$subgrupo'
			order by act_cod_act";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_activo1();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_activo1(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_nom_act') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('cod_activo_hasta', 'value', $data);
	return $oReturn;
}

function generar($aForm = '')
{
	//Definiciones
	global $DSN, $DSN_Ifx;

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oIfxA = new Dbo();
	$oIfxA->DSN = $DSN_Ifx;
	$oIfxA->Conectar();

	$oReturn = new xajaxResponse();

	//variables de sesion
	$array 		 = ($_SESSION['ARRAY_PINTA']);
	$usuario_web = $_SESSION['U_ID'];
	$idempresa   = $_SESSION['U_EMPRESA'];
	$idsucursal  = $_SESSION['U_SUCURSAL'];
	$empresa     = $aForm['empresa'];
	$sucursal    = $aForm['sucursal'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	if (empty($sucursal)) {
		$sucursal = $idsucursal;
	}


	//variables formulario
	$anio 		  =	$aForm['anio'];
	$anio_fin 	  =	$aForm['anio_fin'];
	$mes 		  =	$aForm['mes'];
	$mes_fin 	  =	$aForm['mes_fin'];
	$grupo 		  = $aForm['cod_grupo'];
	$subgrupo	  = $aForm['cod_subgrupo'];
	$activo_desde = $aForm['cod_activo_desde'];
	$activo_hasta = $aForm['cod_activo_hasta'];
	$estado_reva  = $aForm['tipo'];

	$detallado	  = $aForm['detallado'];
	// $fecha_corte  = $aForm['fecha_corte'];
	// $fecha_corte  = fecha_informix_func($fecha_corte);
	// $fechaServer  = date("Y-m-d");
	$fechaini = date("Y-m-d", mktime(0, 0, 0, $mes, 1, $anio));
	$fechafin = date("Y-m-t", mktime(0, 0, 0, $mes_fin, 1, $anio_fin));
	// ARMAR FILTROS
	$filtro = '';
	if (empty($grupo)) {
	} else {
		$filtro = $filtro . " and saegact.gact_cod_gact = '" . $grupo . "'";
	}
	if (empty($subgrupo)) {
	} else {
		$filtro = $filtro . " and saesgac.sgac_cod_sgac = '" . $subgrupo . "'";
	}
	if (empty($activo_desde) && empty($activo_hasta)) {
	} else {
		$filtro = $filtro . " and saeact.act_cod_act between " . $activo_desde . " and " . $activo_hasta;
	}
	if (!empty($estado_reva)) {
		$filtro = $filtro . " and saeact.act_est_reva <> 'N'";
	} else {
		$filtro = $filtro . " and saeact.act_est_reva = 'N'";
	}


	//$oReturn->alert($filtro);
	try {
		$oIfx->QueryT('BEGIN');

		$ctrl_reg = 0;



		unset($_SESSION['ACT_REP_DEPR']);
		$html = '';
		$html .= '<table class="table table-striped table-hover " style="width: 100%; margin-bottom: 0px;">
							<tr class="msgFrm">
								<td class="bg-primary text-center"><h5> Clave </h5></td>
								<td class="bg-primary text-center"><h5> Nombre </h5></td>
								<td class="bg-primary text-center"><h5> F. Calculo </h5></td>
								<td class="bg-primary text-center"><h5> Vida Util </h5></td>
								<td class="bg-primary text-center"><h5> Anio </h5></td>
								<td class="bg-primary text-center"><h5> Mes </h5></td>
								<td class="bg-primary text-center"><h5> Valor Compra </h5> </td>
								<td class="bg-primary text-center"><h5> Valor Residual </h5></td>
								<td class="bg-primary text-center"><h5> Valor Neto </h5></td>
								<td class="bg-primary text-center"><h5> Dep. Anterior </h5></td>
								<td class="bg-primary text-center"><h5> Gasto Depr. </h5></td>
								<td class="bg-primary text-center"><h5> Dep. Acum. </h5></td>
								<td class="bg-primary text-center"><h5> Valor por Depr. </h5></td>
							</tr> ';
		// CAVECERA TABLA

		if ($detallado == 'S') {

			for ($m = $mes; $m <= $mes_fin; $m++) {


				// ULTIMA FILA TOTALES	
				$sumaValorCompra	  = 0;
				$sumaValorResidu	  = 0;
				$sumaValorNeto  	  = 0;
				$sumaDeprAnterior	  = 0;
				$sumaGastoDepr	      = 0;
				$sumaDeprAcumulada	  = 0;
				$sumaValorPorDepr	  = 0;
				// LISTA DEPRECIACION DE ACTIVOS
				$sql = " SELECT saeact.act_cod_act,   
					 saeact.act_clave_act,   
					 saeact.act_nom_act,   
					 saeact.act_val_comp,   
					 saeact.act_vutil_act,   
					 saeact.act_fcmp_act, 
					 saeact.act_fiman_act,  
					 saeact.act_fdep_act,   
					 max(saecdep.cdep_ani_depr) as cdep_ani_depr,   
					 saegact.gact_cod_gact,   
					 saegact.gact_des_gact,   
					 saesgac.sgac_cod_sgac,   
					 saesgac.sgac_des_sgac,   					 
					(select c.cdep_dep_acum 
					 from saecdep c 
					 where c.cdep_cod_acti = saecdep.cdep_cod_acti
					 and c.act_cod_empr = saecdep.act_cod_empr
					 and c.act_cod_sucu = saecdep.act_cod_sucu
					 and c.cdep_ani_depr = $anio
					 and c.cdep_mes_depr = $m) as cdep_dep_acum,
					 sum(saecdep.cdep_gas_depn) as cdep_gas_depn, 					 
					 max(saecdep.cdep_mes_depr) as cdep_mes_depr,
					 DATE_PART('year', act_fiman_act ) anio,
					 DATE_PART('month',act_fiman_act) mes ,
					 saeact.act_vres_act
					FROM saegact,   
						 saesgac,
						 saecdep,   
						 saeact  
			   WHERE ( saegact.gact_cod_gact = saesgac.gact_cod_gact ) and  
					 ( saegact.gact_cod_empr = saesgac.sgac_cod_empr ) and  
					 ( saesgac.sgac_cod_sgac = saeact.sgac_cod_sgac ) and  
					 ( saesgac.sgac_cod_empr = saeact.act_cod_empr ) and  			
					 ( saeact.act_cod_act = saecdep.cdep_cod_acti ) and  
					 ( saeact.act_cod_empr = saecdep.act_cod_empr ) and  
					 ( ( saecdep.act_cod_empr = $empresa ) and
					 ( saecdep.cdep_ani_depr between $anio and $anio_fin ) and  
					 ( saecdep.cdep_mes_depr =$m  ) ) and
					 ( ( (COALESCE(DATE_PART('year', act_fiman_act ),3000))*100+COALESCE(DATE_PART('month',act_fiman_act),13)   )  > ($anio_fin *100 + $m)  )  and
					 ( DATE_PART('year', act_fcmp_act) < $anio_fin or ( DATE_PART('year', act_fcmp_act) = $anio_fin and DATE_PART('month',act_fcmp_act)<= $m))
						$filtro
						GROUP BY 1,2,3,4,5,6,7,8,10,11,12,13,14,17,18,19
						ORDER BY saegact.gact_des_gact, saesgac.sgac_des_sgac, saeact.act_nom_act, cdep_ani_depr, cdep_mes_depr ";
				//echo $sql; exit;
				//$oReturn->alert($sql);
				if ($oIfx->Query($sql)) {
					if ($oIfx->NumFilas() > 0) {
						$i = 1;
						$ctrl_reg++;

						do {
							$clave  	   = $oIfx->f('act_clave_act');
							$nombre 	   = $oIfx->f('act_nom_act');
							$fechaDepre    = $oIfx->f('act_fdep_act');
							$vidaUtil      = $oIfx->f('act_vutil_act');
							$anio 		   = $oIfx->f('cdep_ani_depr');
							$mes 		   = $oIfx->f('cdep_mes_depr');
							$valorCompra   = $oIfx->f('act_val_comp');
							$valorResidu   = $oIfx->f('act_vres_act');
							$serie         = $oIfx->f('act_seri_act');
							$grupo  	   = $oIfx->f('gact_des_gact');
							$subgrupo 	   = $oIfx->f('sgac_des_sgac');
							$deprAnterior  = $oIfx->f('cdep_dep_acum');
							$gastoDepr     = $oIfx->f('cdep_gas_depn');

							$valorNeto     = $valorCompra - $valorResidu;
							$deprAcumulada = $deprAnterior + $gastoDepr;
							$valorPorDepr  = $valorCompra -  $deprAcumulada;

							if ($i < 2) {
								$html .= '<tr>
										<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td> 										
									</tr>
									<tr>										
										<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td> 
									</tr>';
								$html .= '<tr>
										<td>' . $clave . ' </td> 
										<td>' . $nombre . ' </td> 
										<td>' . $fechaDepre . ' </td>
										<td align = right>' . $vidaUtil . ' </td>
										<td align = right>' . $anio . ' </td>
										<td align = right>' . $mes . ' </td>
										<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
									</tr>';
								$totalValorCompra     =  $valorCompra;
								$totalValorResidu     =  $valorResidu;
								$totalValorNeto       =  $valorNeto;
								$totalDeprAnterior    =  $deprAnterior;
								$totalGastoDepr       =  $gastoDepr;
								$totalDeprAcumulada   =  $deprAcumulada;
								$totalValorPorDepr    =  $valorPorDepr;
							} else {
								if ($grupo == $grupoAnt) {
									$totalValorCompra   = $totalValorCompra   + $valorCompra;
									$totalValorResidu   = $totalValorResidu   + $valorResidu;
									$totalValorNeto     = $totalValorNeto     + $valorNeto;
									$totalDeprAnterior  = $totalDeprAnterior  + $deprAnterior;
									$totalGastoDepr     = $totalGastoDepr     + $gastoDepr;
									$totalDeprAcumulada = $totalDeprAcumulada + $deprAcumulada;
									$totalValorPorDepr  = $totalValorPorDepr  + $valorPorDepr;
									if ($subgrupo == $subgrupoAnt) {
										$html .= '<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' </td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
									} else {
										$html .= '<tr>																						
												<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
											</tr>
											<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' </td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
									}
								} else {
									$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
											<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
										</tr>
										<tr>										
											<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td>
										</tr>
										<tr>										
											<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
										</tr>
										<tr>
											<td>' . $clave . ' </td> 
											<td>' . $nombre . ' </td> 
											<td>' . $fechaDepre . ' </td>
											<td align = right>' . $vidaUtil . ' </td>
											<td align = right>' . $anio . ' </td>
											<td align = right>' . $mes . ' </td>
											<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
										</tr>';
									// GUARDAR TOTALES GENERALES
									$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
									$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
									$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
									$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
									$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
									$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
									$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
									// INICIAR TOTALES POR GRUPO
									$totalValorCompra     =  $valorCompra;
									$totalValorResidu     =  $valorResidu;
									$totalValorNeto       =  $valorNeto;
									$totalDeprAnterior    =  $deprAnterior;
									$totalGastoDepr       =  $gastoDepr;
									$totalDeprAcumulada   =  $deprAcumulada;
									$totalValorPorDepr    =  $valorPorDepr;
								}
								// TOTAL POR GRUPOS

								//$totalValorCompra = $totalValorCompra + $valorCompra;
							}
							$grupoAnt	 = $grupo;
							$subgrupoAnt = $subgrupo;
							$i++;
						} while ($oIfx->SiguienteRegistro());
						// ULTIMA FILA POR GRUPOS
						$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
								<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
						// ULTIMA FILA TOTALES	
						$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
						$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
						$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
						$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
						$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
						$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
						$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
						$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTALES" . '</td>   									
								<td align = right style = "color:red">' . number_format($sumaValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
					}
				}
				$oIfx->Free();
			} //CIERRE FOR MES

		} //CIERRE IF DETALLADO

		else {
			// LISTA DEPRECIACION DE ACTIVOS
			$sql = " SELECT saeact.act_cod_act,   
					 saeact.act_clave_act,   
					 saeact.act_nom_act,   
					 saeact.act_val_comp,   
					 saeact.act_vutil_act,   
					 saeact.act_fcmp_act, 
					 saeact.act_fiman_act,  
					 saeact.act_fdep_act,   
					 max(saecdep.cdep_ani_depr) as cdep_ani_depr,   
					 saegact.gact_cod_gact,   
					 saegact.gact_des_gact,   
					 saesgac.sgac_cod_sgac,   
					 saesgac.sgac_des_sgac,   					 
					(select c.cdep_dep_acum 
					 from saecdep c 
					 where c.cdep_cod_acti = saecdep.cdep_cod_acti
					 and c.act_cod_empr = saecdep.act_cod_empr
					 and c.act_cod_sucu = saecdep.act_cod_sucu
					 and c.cdep_ani_depr = $anio_fin
					 and c.cdep_mes_depr = $mes_fin) as cdep_dep_acum,
					 max(saecdep.cdep_gas_depn) as cdep_gas_depn, 					 
					 max(saecdep.cdep_mes_depr) as cdep_mes_depr,
					 DATE_PART('year', act_fiman_act ) anio,
					 DATE_PART('month',act_fiman_act) mes,
					 saeact.act_vres_act,
					 MAX(saecdep.cdep_val_rep1) as cdep_val_rep1

					FROM saegact,   
						 saesgac,
						 saecdep,   
						 saeact  
					WHERE ( saegact.gact_cod_gact = saesgac.gact_cod_gact ) and  
					 ( saegact.gact_cod_empr = saesgac.sgac_cod_empr ) and  
					 ( saesgac.sgac_cod_sgac = saeact.sgac_cod_sgac ) and  
					 ( saesgac.sgac_cod_empr = saeact.act_cod_empr ) and  			
					 ( saeact.act_cod_act = saecdep.cdep_cod_acti ) and  
					 ( saeact.act_cod_empr = saecdep.act_cod_empr ) and  
					 ( ( saecdep.act_cod_empr = $empresa ) and
					 ( saecdep.cdep_fec_depr between '$fechaini' and '$fechafin')) and
					 ( ( (COALESCE(DATE_PART('year', act_fiman_act ),3000))*100+COALESCE(DATE_PART('month',act_fiman_act),13)   )  > ($anio_fin *100 + $mes_fin)  )  and
					 ( DATE_PART('year', act_fcmp_act) < $anio_fin or ( DATE_PART('year', act_fcmp_act) = $anio_fin and DATE_PART('month',act_fcmp_act)<= $mes_fin))
						$filtro
						GROUP BY 1,2,3,4,5,6,7,8,10,11,12,13,14,17,18,19
						ORDER BY saegact.gact_des_gact, saesgac.sgac_des_sgac, saeact.act_nom_act, cdep_ani_depr, cdep_mes_depr ";
			//echo $sql; exit;
			//$oReturn->alert($sql);
			//( saecdep.cdep_ani_depr between $anio and $anio_fin ) and  
			//( saecdep.cdep_mes_depr between $mes and $mes_fin ) ) and
	   		if ($oIfx->Query($sql)) {
				if ($oIfx->NumFilas() > 0) {
					$i = 1;
					$ctrl_reg++;

					do {
						$clave  	   = $oIfx->f('act_clave_act');
						$nombre 	   = $oIfx->f('act_nom_act');
						$fechaDepre    = $oIfx->f('act_fdep_act');
						$vidaUtil      = $oIfx->f('act_vutil_act');
						$anio 		   = $oIfx->f('cdep_ani_depr');
						$mes 		   = $oIfx->f('cdep_mes_depr');
						$valorCompra   = $oIfx->f('act_val_comp');
						$valorResidu   = $oIfx->f('act_vres_act');
						$serie         = $oIfx->f('act_seri_act');
						$grupo  	   = $oIfx->f('gact_des_gact');
						$subgrupo 	   = $oIfx->f('sgac_des_sgac');
						//$deprAnterior  = $oIfx->f('cdep_dep_acum');
						$gastoDepr     = $oIfx->f('cdep_gas_depn');
						$deprAnterior     = $oIfx->f('cdep_val_rep1')?:0;
						$deprAcumulada     = $oIfx->f('cdep_dep_acum');



						$valorNeto     = $valorCompra - $valorResidu;
						$valorPorDepr  = $valorCompra -  $deprAcumulada;

						if ($i < 2) {
							$html .= '<tr>
										<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td> 										
									</tr>
									<tr>										
										<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td> 
									</tr>';
							$html .= '<tr>
										<td>' . $clave . ' </td> 
										<td>' . $nombre . ' </td> 
										<td>' . $fechaDepre . ' </td>
										<td align = right>' . $vidaUtil . ' </td>
										<td align = right>' . $anio . ' </td>
										<td align = right>' . $mes . ' </td>
										<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
									</tr>';
							$totalValorCompra     =  $valorCompra;
							$totalValorResidu     =  $valorResidu;
							$totalValorNeto       =  $valorNeto;
							$totalDeprAnterior    =  $deprAnterior;
							$totalGastoDepr       =  $gastoDepr;
							$totalDeprAcumulada   =  $deprAcumulada;
							$totalValorPorDepr    =  $valorPorDepr;
						} else {
							if ($grupo == $grupoAnt) {
								$totalValorCompra   = $totalValorCompra   + $valorCompra;
								$totalValorResidu   = $totalValorResidu   + $valorResidu;
								$totalValorNeto     = $totalValorNeto     + $valorNeto;
								$totalDeprAnterior  = $totalDeprAnterior  + $deprAnterior;
								$totalGastoDepr     = $totalGastoDepr     + $gastoDepr;
								$totalDeprAcumulada = $totalDeprAcumulada + $deprAcumulada;
								$totalValorPorDepr  = $totalValorPorDepr  + $valorPorDepr;
								if ($subgrupo == $subgrupoAnt) {
									$html .= '<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' </td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
								} else {
									$html .= '<tr>																						
												<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
											</tr>
											<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' </td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
								}
							} else {
								$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
											<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
										</tr>
										<tr>										
											<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td>
										</tr>
										<tr>										
											<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
										</tr>
										<tr>
											<td>' . $clave . ' </td> 
											<td>' . $nombre . ' </td> 
											<td>' . $fechaDepre . ' </td>
											<td align = right>' . $vidaUtil . ' </td>
											<td align = right>' . $anio . ' </td>
											<td align = right>' . $mes . ' </td>
											<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
										</tr>';
								// GUARDAR TOTALES GENERALES
								$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
								$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
								$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
								$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
								$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
								$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
								$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
								// INICIAR TOTALES POR GRUPO
								$totalValorCompra     =  $valorCompra;
								$totalValorResidu     =  $valorResidu;
								$totalValorNeto       =  $valorNeto;
								$totalDeprAnterior    =  $deprAnterior;
								$totalGastoDepr       =  $gastoDepr;
								$totalDeprAcumulada   =  $deprAcumulada;
								$totalValorPorDepr    =  $valorPorDepr;
							}
							// TOTAL POR GRUPOS

							//$totalValorCompra = $totalValorCompra + $valorCompra;
						}
						$grupoAnt	 = $grupo;
						$subgrupoAnt = $subgrupo;
						$i++;
					} while ($oIfx->SiguienteRegistro());
					// ULTIMA FILA POR GRUPOS
					$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
								<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
					// ULTIMA FILA TOTALES	
					$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
					$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
					$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
					$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
					$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
					$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
					$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
					$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTALES" . '</td>   									
								<td align = right style = "color:red">' . number_format($sumaValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
				}
			}
			$oIfx->Free();
		} //CIERRE ELSE


		$html .= '</table>';

		if ($ctrl_reg != 0) {
			$_SESSION['ACT_REP_DEPR'] = $html;
		} else {
			$html = '<div style="font-size:14px;" ><b>..Sin Datos..<b/></div>';
		}


		$oReturn->assign("reporte", "innerHTML", $html);
		$oReturn->alert('Proceso Terminado con Exito');
		$oIfx->QueryT('COMMIT WORK;');
	} catch (Exception $e) {
		$oCon->QueryT('ROLLBACK');
		$oReturn->alert($e->getMessage());
	}
	return $oReturn;
}


/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
