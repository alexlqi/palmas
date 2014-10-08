<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
?>
<script src="js/formularios.js"></script>
<script src="js/salones.js"></script>
<style>
.salon{
	padding:5px 10px;
	margin-right:10px;
	margin-bottom:10px;
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	display:inline-block;
	font-weight:bold;
}
</style>
<form id="f_salones" class="formularios">
  <h3 class="titulo_form">Salones</h3>
  	<input type="hidden" name="id_salon" class="id_salon" value="" />
    <div class="campo_form">
        <label class="label_width">Nombre del salón</label>
        <input type="text" name="nombre" class="nombre text_mediano">
    </div>
   	<div align="right">
        <input type="button" class="guardar_individual guardarb" value="GUARDAR" data-m="individual" />
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;" />
        <input type="button" class="nueva" value="NUEVA" />
    </div>
</form>
<h2>Salones de la empresa</h2>
<?php
	try{
		$bd=new PDO($dsnw,$userw,$passw,$optPDO);
		$id_empresa=$_SESSION["id_empresa"];
		$res=$bd->query("SELECT * FROM salones WHERE id_empresa=$id_empresa;");
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			echo '<div class="salon fondo_azul">'.$v["nombre"].'</div>';
		}
	}catch(PDOException $err){
		echo '<tr><td colspan="20">Error encontrado: '.$err->getMessage().'</td></tr>';
	}
?>
<div align="right">
    <input type="button" class="volver" value="VOLVER">
</div>