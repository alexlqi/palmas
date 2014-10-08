<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
?>
<script src="js/formularios.js"></script>
<form id="f_tablas" class="formularios">
<h3 class="titulo_form">Unidades</h3>
	<input type="hidden" name="id_tabla" class="id_tabla" />
    <input type="hidden" name="grupo" class="grupo" value="unidades" />
<div class="campo_form">
    <label class="label_width">Abreviación de unidad</label>
    <input type="text" name="nombre" class="nombre requerido text_mediano">
</div>
<div class="campo_form">
    <label class="label_width">Nombre de unidad</label>
    <input type="text" name="descripcion" class="descripcion text_mediano">
</div>
	<input type="button" class="guardar_individual" value="GUARDAR" data-m="individual">
    <input type="button" class="modificar" value="MODIFICAR" style="display:none;">
</form>
<div align="right">
	<input type="button" class="volver" value="VOLVER">
</div>
</div>