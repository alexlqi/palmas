<?php session_start(); 
$empresaid=$_SESSION["empresaid"];
?>
<style>
/* estilos para formularios */
.campo_form{
	margin:4px 0;
}
.text_corto{
	width:80px;
}
.text_mediano{
	width:150px;
}
.text_largo{
	width:400px;
}
.text_full_width{
	width:100%;
}
.text_half_width{
	width:50%;
}
.volver, .guardar_pivote, .guardar_individual, .guardar_referenciado{
	padding:20px;
}
.guardar_individual{
	position: absolute;
	bottom: 10;
	right: 10;
}
</style>
<script>
$(document).ready(function(e) {
	//quita el fondo rojo de falto llenar
	$(".requerido").on("keyup",function(e){
		if($(this).val()!=""){
			$(this).removeClass("falta_llenar");
		}
	});
	
	//escribe en mayuscula la letra del cmapo con la clase mayuscula
	$('.mayuscula').keyup(function(){
		this.value = this.value.toUpperCase();
	});

	//inhabilita los campos con la clase deshabilitar
	$(".disabled").attr("disabled","disabled");
	
	//quita el atributo name
	$(".remueve_name").removeAttr("name");
	
    $(".volver").click(function(e) {
    	$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
			$("#botones_modulo").fadeIn(rapidez);
		});
    });
	
	$("select").change(function(e) {
		campo="."+$(this).attr("data-campo");
		$(campo).val($(this).val());
		clave=$('option:selected', this).attr("data-clave");
		nombre=$('option:selected', this).attr("data-nombre");
		$(".clave").val(clave);
		$(".nombre").val(nombre);
    });
	
	
	$(".guardar_pivote").click(function(e) {
	  if(requerido("#formularios_modulo")){
		//metodo en que se va a guardar
		metodo=$(this).attr('data-m');
		
		//datos de los formularios
		datos={};
        $.each($("form"),function(i, v){
			datos[$(this).attr("id")]=$(this).serializeArray();
		});
		
		//procesamiento de datos
		$.ajax({
			url:'scripts/s_guardar_form.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'metodo':metodo,
				'datos':datos
			},
			success: function(r){
				if(r.continuar){
					alert(r.info);
					$(".volver").click();
				}else{
					alert(r.info);
				}
			}
		});
	  }//if del requerido
    });
	
	$(".guardar_referenciado").click(function(e) {
        datos={};
        $.each($("form"),function(i, v){
			datos[$(this).attr("id")]=$(this).serializeArray();
		});
		console.log(datos);
    });
	
	$(".guardar_individual").click(function(e) {
	  if(requerido("#formularios_modulo")){
		//metodo en que se va a guardar
		metodo=$(this).attr('data-m');
		tabla=$(this).parent().attr("id");;
		forma="#"+$(this).parent().attr("id");
		
		//siempre usar este método cuando se vaya a utilizar el serialize + otros campos
		datos={};
		$.each($(forma).serializeArray(),function(i,v){
			//console.log(v["name"]+" "+v["value"]);
			datos[v["name"]]=v["value"];
		});
		
		//procesamiento de datos
		$.ajax({
			url:'scripts/s_guardar_form.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'metodo':metodo,
				'tabla':tabla,
				'datos':datos
			},
			success: function(r){
				if(r.continuar){
					alert(r.info);
					$(".volver").click();
				}else{
					alert(r.info);
				}
			}
		});
	  }//if del requerido
    });
});
function requerido(seccion){
	selector=seccion + " .requerido";
	continuar=true;
	$.each($(selector),function(i,v){
		console.log(v);
		if($(this).val()==""){
			$(this).addClass("falta_llenar");
			continuar=false;
		}
	});
	return continuar;
}
</script>
<?php 
include("datos.php");
include_once("formas.php");
include_once("campos.php");

$metodo=$_POST["metodo"];
$tabla=$formas[$metodo][$_POST["forma"]];
try{
	
	if(!is_array($tabla)){
	/*ÚNICA TABLA*/
		//lee los campos de la tabla
		$campos=$campos[$tabla];
		
		//escribe el formulario para cada tabla
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
		$res=$bd->query("DESCRIBE $tabla");
		echo "<form id='f_$tabla' class='formularios'>";
		echo "<h3 class='titulo_form'>".strtoupper($tabla)."</h3>";
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $row){
		//[0]-> boolean
		//[1]1-> label
		//[2]2-> clases
		  if($campos[$metodo][$row["Field"]][0]){
			echo '<div class="campo_form">';
			echo '<label class="label_width">'.$campos[$row["Field"]][1].'</label>';
			echo '<input type="'.$campos[$row["Field"]][3].'" name="'.$row["Field"].'" class="'.$campos[$row["Field"]][2].'" /><br>';
			echo '</div>';
		  }
		}
		echo "</form>";
	} else {
	/*VARIAS TABLAS*/
		foreach($tabla as $f){
			//lee los campos de la tabla/formulario de campos.php
			//$f[0] es el nombre de la tabla de la iteración actual
			$campos_form=$campos[$metodo][$f[0]];
			
			//escribe el formulario para cada tabla
			$bd=new PDO($dsnw, $userw, $passw, $optPDO);
			$res=$bd->query("DESCRIBE ".$f[0]);
			echo "<form id='f_".$f[0]."' class='formularios'>";
			echo "<h3 class='titulo_form'>".strtoupper($f[1])."</h3>";
			foreach($res->fetchAll(PDO::FETCH_ASSOC) as $row){
			//$row["Field"] es para sacar el nombre de la tabla/form de la iteración en curso
			//[0]-> boolean para ponerlo o no
			//[1]-> label
			//[2]-> clases
			//[3]-> type
			  if($campos_form[$row["Field"]][0]){
				$style="";
				//si el campo label está vacio se oculta el div del input display:none
				if($campos_form[$row["Field"]][1]==""){
					$style='style="display:none;"';
				}
				
				//if para los diferentes tipos de inputs de formulario
				$tipo=$campos_form[$row["Field"]][3];
				$buscarTabla=$f[0];
				$buscarCampo=$row["Field"];
				if($tipo=="select"){
					$resDos=$bd->query("SELECT $buscarCampo, clave, nombre FROM $buscarTabla WHERE id_empresa=$empresaid;");
					$displaycampo='<'.$tipo.' data-campo="'.$row["Field"].'" name="'.$row["Field"].'" class="'.$campos_form[$row["Field"]][2].'">';
					$displaycampo.='<option selected="selected" disabled="disabled">Selecciona un proveedor</option>';
					foreach($resDos->fetchAll(PDO::FETCH_ASSOC) as $rowDos){
						$displaycampo.="<option value='".$rowDos[$row["Field"]]."' ";
						$displaycampo.='data-clave="'.$rowDos["clave"].'"';
						$displaycampo.='data-nombre="'.$rowDos["nombre"].'"';
						$displaycampo.=">".$rowDos["nombre"]."</option>";
					}
					$displaycampo.="</$tipo>";
				}else if($tipo=="textarea"){
					$displaycampo='<'.$tipo.' name="'.$row["Field"].'" class="'.$campos_form[$row["Field"]][2].'">'."</$tipo>";
				}else{
					$displaycampo='<input type="'.$tipo.'" name="'.$row["Field"].'" class="'.$campos_form[$row["Field"]][2].'" /><br>';
				}
				
				echo '<div class="campo_form" '.$style.'>';
				echo '<label class="label_width">'.$campos_form[$row["Field"]][1].'</label>';
				echo $displaycampo;
				echo '</div>';
			  }
			}
			//si el método es individual entonces pone un boton guardar por cada formulario
			if($metodo=="individual"){
			  echo '<input type="button" class="guardar_individual" value="GUARDAR" data-m="'.$metodo.'" />';
		    }
			echo "</form>";//*/
		}
	}
}catch(PDOException $err){
	echo "Error encontrado: ".$err->getMessage();
}
?>

<div align="right">
	<?php if($metodo=="pivote"){ ?>
      <input type="button" class="guardar_pivote" value="GUARDAR" data-m="<?php echo $metodo; ?>" />
	<?php } ?>
    <?php if($metodo=="referenciado"){ ?>
      <input type="button" class="guardar_referenciado" value="GUARDAR" data-m="<?php echo $metodo; ?>" />
    <?php } ?>
    <input type="button" class="volver" value="VOLVER" />
</div>