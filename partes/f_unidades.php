<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$emp=$_SESSION["id_empresa"];
$units=array();

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT * FROM unidades WHERE id_empresa=$emp;";
	$res=$bd->query($sql);
	if($res->rowCount()>0){
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
			$id=$d["id_unidad"];
			unset($d["id_unidad"]);
			$units[$id]=$d;
		}
	}else{
		$units=NULL;
	}
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}
?>
<script src="js/formularios.js"></script>
<form id="f_unidades" class="formularios">
<h3 class="titulo_form">Unidades</h3>
	<input type="hidden" name="id_unidad" class="id_unidad" />
<div class="campo_form">
    <label class="label_width">Abreviación de unidad</label>
    <input type="text" name="abrev" class="abrev requerido text_mediano">
</div>
<div class="campo_form">
    <label class="label_width">Nombre de unidad</label>
    <input type="text" name="unidad" class="unidad text_mediano">
</div>
<div align="right">
	<input type="button" class="guardar_individual" value="GUARDAR" data-m="individual">
    <input type="button" class="modificar" value="MODIFICAR" style="display:none;">
</div>
</form>
<div align="right">
	<input type="button" class="volver" value="VOLVER">
</div>
</div>
<div class="formularios">
<h3 class="titulo_form">Unidades</h3>
	<table style="width:100%;">
    	<tr>
        	<th>NOMBRE<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
            <th>ABREVIACIÓN</th>
            <th>ELIMINAR</th>
        </tr>
        
    <?php if(count($units)>0){foreach($units as $id=>$d){
		echo '<tr>';
		echo '<td class="dbc" data-action="clave">'.$d["unidad"].'</td>';
		echo '<td>'.$d["abrev"].'</td>';
		echo '<td><img src="../img/cruz.png" height="20" onclick="eliminar('.$id.',this);" /></td>';
		echo '</tr>';
	}//foreach
	}//if end ?>
    </table>
</div>