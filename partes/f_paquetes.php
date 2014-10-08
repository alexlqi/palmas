<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$id_emp=$_SESSION["id_empresa"];

try{
	$areas="SELECT * FROM areas WHERE id_empresa=$id_emp;";
	$familias="SELECT * FROM familias WHERE id_empresa=$id_emp;";
	$subfamilias="SELECT * FROM subfamilias WHERE id_empresa=$id_emp;";
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	$res=$bd->query($areas);
	$areas="<option>Áreas</option>";
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$areas.='<option value="'.$v["id_area"].'">'.$v["nombre"].'</option>';
	}
	$res=$bd->query($familias);
	$familias="<option>Familias</option>";
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$familias.='<option value="'.$v["id_familia"].'">'.$v["nombre"].'</option>';
	}
	$res=$bd->query($subfamilias);
	$subfamilias="<option>Subfamilias</option>";
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$subfamilias.='<option value="'.$v["id_subfamilia"].'">'.$v["nombre"].'</option>';
	}
}catch(PDOException $err){
	echo $err->getMessage();
}

?>
<style>
#articulos_tabla{
	margin:0 auto;
}
</style>
<script src="js/formularios.js"></script>
<script src="js/paquetes.js"></script>
<form id="f_paquetes" class="formularios">
<h3 class="titulo_form">PAQUETE</h3>
  <input type="hidden" name="id_paquete" class="id_paquete">
    <div class="campo_form">
        <label class="label_width">CLAVE</label>
        <input type="text" name="clave" class="requerido clave mayuscula text_corto" value="<?php nCvePaq(); ?>">
    </div>
    <div class="campo_form">
        <label class="label_width">NOMBRE</label>
        <input type="text" name="nombre" class="nombre requqerido text_largo">
    </div>
    <div class="campo_form">
        <label class="label_width">Descripción</label>
        <textarea name="descripcion" class="descripcion"></textarea>
    </div>
    <div class="campo_form">
        <label class="label_width">Unidades</label>
        <input type="text" name="unidades" class="unidades">
    </div>
</form>
<form id="f_listado_precios" class="formularios">
<h3 class="titulo_form">Costos y Precios</h3>
  <input type="hidden" name="id_item" class="id_item" />
  <input type="hidden" name="id_empresa" class="id_empresa" value="<?php empresa(); ?>">
    <div class="campo_form">
        <label class="label_width">Precio de compra</label>
        <input type="text" name="compra" class="compra requerido" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio de lista</label>
        <input type="text" name="precio1" class="precio1 requerido" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio de mayoreo</label>
        <input type="text" name="precio2" class="precio2" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio de otro concepto</label>
        <input type="text" name="precio3" class="precio3" />
    </div>
    <div class="campo_form">
        <label class="label_width">Precio de otro concepto</label>
        <input type="text" name="precio4" class="precio4" />
    </div>
</form>
    <div align="right">
        <input type="button" class="guardar" value="GUARDAR" data-m="pivote">
        <input type="button" class="modificar" value="MODIFICAR" style="display:none;">
        <input type="button" class="volver" value="VOLVER">
    </div>
<div id="articulos" class="formularios">
  <h3 class="titulo_form">Artículos en el paquete</h3>
  <div align="center">
  	  <input type="hidden" class="id_paquete" />
      <select class="areas" onchange="verArticulos(this);" data-zone="areas"><?php echo $areas; ?></select>
      <select class="familias" onchange="verArticulos(this);" data-zone="familias"><?php echo $familias; ?></select>
      <select class="subfamilias" onchange="verArticulos(this);" data-zone="subfamilias"><?php echo $subfamilias; ?></select>
      <select class="id_articulo"></select>
      <input type="text" class="cantidad numerico" size="10" placeholder="cantidad" />
      <input type="button" class="agregar_articulo" value="Agregar al paquete" />
  </div>
  <table id="articulos_tabla">
      <tr class="noborrar">
      	<th>Área</th>
        <th>Familia</th>
        <th>Subfamilia</th>
        <th width="250">Articulo</th>
        <th width="100">Unidades</th>
        <th width="100">Cantidad</th>
        <th width="100">Quitar</th>
      </tr>
  </table>
</div>
<script>
$(document).ready(function(e) {
	$(".numerico").numeric();
    $( ".clave" ).keyup(function(e){
		_this=$(this);
		if(e.keyCode!=8 && _this.val()!=""){
			if(typeof timer=="undefined"){
				timer=setTimeout(function(){
					buscarClave();
				},300);
			}else{
				clearTimeout(timer);
				timer=setTimeout(function(){
					buscarClave();
				},300);
			}
		}else{
			resetform();
		}
    }); //termina buscador de cotizacion
	$(".agregar_articulo").click(function(e) {
		paq=$("#articulos .id_paquete").val();
		art=$(".id_articulo").val();
		cant=$(".cantidad").val();
		if( (art!="null" || art!="") && cant!="" && paq!="" ){
			$.ajax({
				url:'scripts/s_agregar_art_paq.php',
				cache:false,
				type:'POST',
				data:{
					'paq':paq,
					'art':art,
					'cant':cant
				},
				success: function(r){
					if(r.continuar){
						buscaArtPaq(paq);
						alerta("info",r.info);
					}else{
						alerta("error",r.info);
					}
				}
			});
		}else{
		   alert("Artículo no seleccionado o cantidad vacía");
		}
    });
});
function buscarClave(){
	$(".id_paquete").val('');
	dato=$(".clave").val();
	input=$(".clave");
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/s_busca_paq_get.php",
	  cache:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		clave=$(".clave").val();
		resetform();
		$(".clave").val(clave);
		$.each(r,function(i,v){
			$("."+i).text(v);
			$("."+i).val(v);
		});
		buscaArtPaq(r.id_paquete);
		//asigna el id de cotización
		input.removeClass("ui-autocomplete-loading");
	  }
	});
}

//función para ver los articulos seun el afs
function verArticulos(e){
	elem=$(e);
	$(".id_articulo").html('');
	zona=elem.attr("data-zone");
	id=elem.find("option:selected").val();
	$.ajax({
		url:'scripts/s_get_articulo.php',
		cache:false,
		type:'POST',
		data:{
			'zona':zona,
			'id':id
		},
		success: function(r){
			$(".id_articulo").html(r);
		}
	});
}

//function para buscar los articulos dentro del paquete
function buscaArtPaq(paq){
	$.ajax({
		url:'scripts/s_get_art_paq.php',
		cache:false,
		type:'POST',
		data:{
			'paq':paq,
		},
		success: function(r){
			$("tr:not(.noborrar)").remove();
			$("#articulos_tabla").append(r);
		}
	});
}
function eliminar(e){
	e=$(e);
	id=e.attr("data-row");
	mouseLoad(true);
	$.ajax({
		url:'scripts/s_quitar_art_paq.php',
		cache:false,
		type:'POST',
		data:{
			id:id
		},
		success: function(r){
			mouseLoad(false);
			if(r.continuar){
				alerta("info","Articulo removido exitosamente");
				e.parent().parent().remove();
			}else{
				alerta("error",r.info);
			}
		}
	});
}
</script>