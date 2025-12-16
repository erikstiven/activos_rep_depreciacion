<? /********************************************************************/ ?>
<? /* NO MODIFICAR ESTA SECCION*/ ?>
<? include_once('../_Modulo.inc.php');?>
<? include_once(HEADER_MODULO);?>
<? if ($ejecuta) { ?>
<? /********************************************************************/ ?>

<script>
/* xajax.callback.global.onResponseDelay = show_load;
xajax.callback.global.onComplete = hide_load;
 */

        function genera_formulario(){
                xajax_genera_cabecera_formulario();
        }

	function cerrar_ventana(){
            CloseAjaxWin();
        }
	
	function guardar( ){
		if (ProcesarFormulario()==true){
			xajax_guardar(xajax.getFormValues("form1") );
                }
	}				
	
	function cerrar_ventana(){
			CloseAjaxWin();
	}           

</script>
<div align="center">
    <form id="form1" name="form1" action="javascript:void(null);">
      <table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
        <tr>
          	<td valign="top" align="center">
            	<div id="DivPresupuesto"></div>
         	</td>
        </tr>
		<tr>
          	<td valign="top" align="center">
            	<div id="Paginacion"></div>
         	</td>
        </tr>
        <tr>
          	<td valign="top" align="center">
            	<div id="Reporte_Xml23"></div>
         	</td>
        </tr>
		<tr>
          	<td valign="top" align="center">
            	<div id="DivReporte"></div>
         	</td>
        </tr>
		<tr>
          	<td valign="top" align="center">
            	<div id="DivEfectivo"></div>
         	</td>
        </tr>
        </tr>
		<tr>
          	<td valign="top" align="center">
            	<div id="DivCredito"></div>
         	</td>
        </tr>
        </tr>
		<tr>
          	<td valign="top" align="center">
            	<div id="DivCheque"></div>
         	</td>
        </tr>
        </tr>
		<tr>
          	<td valign="top" align="center">
            	<div id="DivRemesa"></div>
         	</td>
        </tr>
         </tr>
		<tr>
          	<td valign="top" align="center">
            	<div id="DivTarjeta"></div>
         	</td>
        </tr>
         </tr>
		<tr>
          	<td valign="top" align="center">
            	<div id="DivRetencion"></div>
         	</td>
        </tr>

      </table>
  </form>
</div>

<script> genera_formulario() </script>
<? /********************************************************************/ ?>
<? /* NO MODIFICAR ESTA SECCION*/ ?>
<? } ?>     			
<? include_once(FOOTER_MODULO); ?>
<? /********************************************************************/ ?>