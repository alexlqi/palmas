<?php session_start(); 
include("../scripts/datos.php");
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
<form id='f_articulos' class='formularios'>
<h3 class='titulo_form'>ARTÍCULOS</h3>
    <div class="campo_form" style="">
        <label></label>
        <input type="text" name="" class="" />
    </div>
</form>
<div align="right">
    <input type="button" class="guardar_referenciado" value="GUARDAR" data-m="<?php echo $metodo; ?>" />
    <input type="button" class="volver" value="VOLVER" />
</div>