<?php
	
	include_once('../../Include/config.inc.php');
	include_once(path(DIR_INCLUDE).'conexiones/db_conexion.php');
	include_once(path(DIR_INCLUDE).'comun.lib.php');

	if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    global $DSN_Ifx, $DSN;

	$oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    //varibales de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    
    if (isset($_REQUEST['nomGrupo'])){
        $nomGrupo = $_REQUEST['nomGrupo'];
		if(($nomGrupo!='0')&&($nomGrupo!='')){
			$con_nom="where (pais_des_pais like upper ('%$nomGrupo%'))";
		}else{
			$con_nom=null;
		}
	}
    else{
        $nomGrupo = null;
	}

    $tabla = '';

    $sql = "select pais_cod_pais, pais_cod_grup, 
			(select grup_des_grup from saegrup where grup_cod_grup = pais_cod_grup) as nom_grupo , 
			pais_des_pais, pais_des_naci, pais_cod_inte
    		from saepais
			$nomGrupo";
			
			//echo $sql;exit;
	$i=1;
    if($oIfx->Query($sql)){
    	if($oIfx->NumFilas() > 0){
    		do{
				$pais_cod_pais = $oIfx->f('pais_cod_pais');
				$pais_cod_grup = $oIfx->f('pais_cod_grup');
				$nom_grupo     = $oIfx->f('nom_grupo');
				$pais_des_pais = $oIfx->f('pais_des_pais');
				$pais_des_naci = $oIfx->f('pais_des_naci');
				$pais_cod_inte = $oIfx->f('pais_cod_inte');
				//$pais_des_pais = str_replace("'", " ", $pais_des_pais);

				$img = '<div align=\"center\"><div class=\"btn btn-success btn-sm\" onclick=\"seleccionaItem(\'' . $pais_cod_pais . '\', \'' . $pais_cod_grup . '\', \'' . $pais_des_pais . '\', \''.$pais_des_naci.'\', \''.$pais_cod_inte.'\')\"><span class=\"glyphicon glyphicon-ok\"><span></div> </div>';
    			$tabla.='{
				  "codigo_pais":"'.$pais_cod_pais.'",
				  "continente":"'.$nom_grupo.'",
				  "nombre_pais":"'.$pais_des_pais.'",
				  "selecciona":"'.$img.'"
				},';
				$i++;
			}while($oIfx->SiguienteRegistro());
    	}
	}

	
	$oIfx->Free();

	//eliminamos la coma que sobra
	$tabla = substr($tabla,0, strlen($tabla) - 1);

	echo '{"data":['.$tabla.']}';
	
?>