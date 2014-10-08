<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
?>
<script src="js/formularios.js"></script>
<script src="js/tipo_evento.js"></script>
<style>
#f_tipo_evento .guardar_individual{
	position:relative;
}
#f_tipo_evento .modificar{
	position:relative;
}
</style>
<form id="f_tipo_evento" class="formularios">
  <h3 class="titulo_form">Tipo de evento</h3>
  	<input type="hidden" name="id_tipo" class="id_tipo" value="" />
    <div class="campo_form">
        <label class="label_width">Nombre de evento</label>
        <input type="text" name="nombre" class="nombre text_mediano">
    </div>
   	<div align="right">
        <input type="button" class="guardar_individual guardar" value="GUARDAR" data-m="individual" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
        <input type="button" class="nueva" value="NUEVA" />
    </div>
    
</form>
<div align="right">
    <input type="button" class="volver" value="VOLVER">
</div>