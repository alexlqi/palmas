<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");?>
<script src="js/formularios.js"></script>
<script>
$(document).ready(function(e) {
    $(".nombre").focusout(function(e) {
		$(".razon").val($(this).val());
    });
	$(".direccion, .colonia, .ciudad, .estado, .cp").focusout(function(e) {
		$("."+$(this).attr("name")).val($(this).val());
    });
});
</script>
<form id="f_proveedores" class="formularios">
  <h3 class="titulo_form">PROVEEDOR</h3>
  	<input type="hidden" name="id_proveedor" class="id_proveedor" />
    <div class="campo_form">
    <label class="label_width">CLAVE</label>
    <input type="text" name="clave" class="clave text_corto requerido mayuscula" value="<?php nCveProv(); ?>">
    </div>
    <div class="campo_form">
    <label class="label_width">Nombre</label>
    <input id="nombre_buscar" type="text" name="nombre" class="nombre text_largo">
    </div>
    <div class="campo_form">
    <label class="label_width">Límite de crédito</label>
    <input type="text" name="limitecredito" class="limitecredito text_mediano">
    </div>
    <input class="boton_dentro" type="reset" value="Limpiar" />
</form>
<form id="f_proveedores_contacto" class="formularios">
  <h3 class="titulo_form">DATOS DE CONTACTO</h3>
  <input type="hidden" name="id" class="id" />
  <input type="hidden" name="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>" />
    <div class="campo_form">
        <label class="label_width">CLAVE</label>
        <input type="text" name="clave" class="requerido mayuscula clave">
    </div>
    <div class="campo_form">
        <label class="label_width">Dirección</label>
        <input type="text" name="direccion" class="direccion">
    </div>
    <div class="campo_form">
        <label class="label_width">Colonia</label>
        <input type="text" name="colonia" class="colonia">
    </div>
    <div class="campo_form">
        <label class="label_width">Ciudad</label>
        <input type="text" name="ciudad" class="ciudad">
    </div>
    <div class="campo_form">
        <label class="label_width">Estado</label>
        <input type="text" name="estado" class="estado">
    </div>
    <div class="campo_form">
        <label class="label_width">Código Postal</label>
        <input type="text" name="cp" class="cp">
    </div>
    <div class="campo_form">
        <label class="label_width">Telefono</label>
        <input type="text" name="telefono" class="telefono">
    </div>
    <div class="campo_form">
        <label class="label_width">Celular o Nextel</label>
        <input type="text" name="celular" class="celular">
    </div>
    <div class="campo_form">
        <label class="label_width">E-mail</label>
        <input type="text" name="email" class="email">
    </div>
</form>
<form id="f_proveedores_fiscal" class="formularios">
  <h3 class="titulo_form">INFORMACIóN FISCAL</h3>
  <input type="hidden" name="id" class="id" />
  <input type="hidden" name="id_empresa" value="<?php echo $_SESSION["id_empresa"]; ?>" />
    <div class="campo_form">
        <label class="label_width">RFC</label>
        <input type="text" name="rfc" class="requerido mayuscula rfc">
    </div>
    <div class="campo_form">
        <label class="label_width">Razón social</label>
        <input type="text" name="razon" class="requerido razon">
    </div>
    <div class="campo_form">
        <label class="label_width">Nombre Comercial</label>
        <input type="text" name="nombrecomercial" class="requerido nombrecomercial">
    </div>
    <div class="campo_form">
        <label class="label_width">Direccion Fiscal</label>
        <input type="text" name="direccion" class="requerido direccion">
    </div>
    <div class="campo_form">
        <label class="label_width">Colonia</label>
        <input type="text" name="colonia" class="requerido colonia">
    </div>
    <div class="campo_form">
        <label class="label_width">Ciudad</label>
        <input type="text" name="ciudad" class="requerido ciudad">
    </div>
    <div class="campo_form">
        <label class="label_width">Estado</label>
        <input type="text" name="estado" class="requerido estado">
    </div>
    <div class="campo_form">
        <label class="label_width">Código Postal</label>
        <input type="text" name="cp" class="requerido cp">
    </div>
    </form>
    <div align="right">
        <input type="button" class="guardar" value="GUARDAR" data-accion="nuevo" data-m="pivote" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
    	<input type="button" class="volver" value="VOLVER">
    </div>
</div>