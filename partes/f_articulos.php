<?php session_start(); 
include("../scripts/funciones.php");
include("../scripts/func_form.php");
include("../scripts/datos.php");
$emp=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT
		articulos.*,
		listado_precios.*,
		areas.nombre as area,
		familias.nombre as familia,
		subfamilias.nombre as subfamilia
	FROM articulos
	LEFT JOIN listado_precios ON articulos.id_articulo=listado_precios.id_articulo
	LEFT JOIN areas ON articulos.area=areas.id_area
	LEFT JOIN familias ON articulos.familia=familias.id_familia
	LEFT JOIN subfamilias ON articulos.subfamilia=subfamilias.id_subfamilia
	WHERE articulos.id_empresa=$emp AND articulos.activo=1;";
	$res=$bd->query($sql);
	$articulos=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
		$articulos[$d["id_articulo"]]=$d;
	}
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}
?>
<style>
.dbc{
	cursor:pointer;
	color:#900;
}
.ui-autocomplete-loading {
	background: white url('img/load.gif') right center no-repeat;
}
</style>
<script src="js/formularios.js"></script>
<form id="f_articulos" class="formularios">
<h3 class="titulo_form">ARTÍCULO</h3>
  <input type="hidden" name="id_articulo" class="id_articulo">
    <div class="campo_form">
        <label class="label_width">CLAVE</label>
        <input type="text" name="clave" class="requerido clave mayuscula" value="">
    </div>
    <div class="campo_form">
        <label class="label_width">NOMBRE</label>
        <input type="text" name="nombre" class="requqerido nombre">
    </div>
    <div class="campo_form">
        <label class="label_width">Descripción</label>
        <input type="text" name="descripcion" class="descripcion">
    </div>
    <div class="campo_form">
        <label class="label_width">Unidades</label>
        <input type="text" name="unidades" class="unidades">
    </div>
    <div class="campo_form">
    	<label class="label_width">Pertenece a:</label>
        <select name="area" class="area"><option selected="selected" value="">Área</option><?php afs("area"); ?></select>
        <select name="familia" class="familia"><option selected="selected" value="">Familia</option><?php afs("familia"); ?></select>
        <select name="subfamilia" class="subfamilia"><option selected="selected" value="">SubFamilia</option><?php afs("subfamilia"); ?></select>
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
    <input type="button" class="volver" value="VOLVER">
</div>
</div>

<div class="formularios">
<h3 class="titulo_form">Listado de artículos registrados</h3>
	<table style="width:100%;">
    	<tr>
        	<th>CLAVE<br /><font style="font-size:0.4em; color:#999;">Doble Clic<br />para modificar</font></th>
            <th>NOMBRE</th>
            <th>ÁREA</th>
            <th>FAMILIA</th>
            <th>SUBFAMILIA</th>
            <th>ELIMINAR</th>
        </tr>
        
    <?php if(count($articulos)>0){foreach($articulos as $art=>$d){
		echo '<tr>';
		echo '<td class="dbc" data-action="clave">'.$d["clave"].'</td>';
		echo '<td>'.$d["nombre"].'</td>';
		echo '<td>'.$d["area"].'</td>';
		echo '<td>'.$d["familia"].'</td>';
		echo '<td>'.$d["subfamilia"].'</td>';
		echo '<td><img src="../img/cruz.png" height="20" onclick="eliminar('.$art.',this);" /></td>';
		echo '</tr>';
	}//foreach
	}//if end ?>
    </table>
</div>
<script>
$(document).ready(function(e) {
    $( ".clave" ).keyup(function(e){
		_this=$(this);
		continuar=true;
		if(_this.val()==""){
			continuar=false;
			resetform();
		}
		if(e.keyCode<37 && e.keyCode>40){
			continuar=false;
		}
		if(continuar){
			if(typeof timer=="undefined"){
				timer=setTimeout(function(){
					buscarClaveArt()
				},300);
			}else{
				clearTimeout(timer);
				timer=setTimeout(function(){
					buscarClaveArt();
				},300);
			}
		}
    }); //termina buscador de cotizacion
	$(".dbc").dblclick(function(e) {
        accion=$(this).attr("data-action");
		val=$(this).text();
		switch(accion){
			case 'clave':
				$(".clave").val(val);
				scrollTop();
				buscarClaveArt();
			break;
		}
    });
});
function buscarClaveArt(){
	dato=$(".clave").val();
	input=$(".clave");
	input.addClass("ui-autocomplete-loading");
	$.ajax({
	  url:"scripts/s_busca_art_get.php",
	  cache:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		clave=$(".clave").val();
		resetform();
		$(".clave").val(clave);
		$.each(r,function(i,v){
			$("."+i).val(v);
		});
		//asigna el id de cotización
		input.removeClass("ui-autocomplete-loading");
	  }
	});
}

//resetar todos los forms
function resetform(){
	cve=$(".clave").val();
	$.each($("body").find("form"),function(i,v){
		this.reset();
	});
	$(".clave").val(cve);
}

function eliminar(id,e){
	_this=$(e).parent().parent();
	if(confirm("¿Seguro que desa eliminar el articulo?")){
		$.ajax({
			url:'scripts/s_eliminar_articulo.php',
			cache:false,
			type:'POST',
			data:{
				art:id
			},
			success: function(r){
				if(r.continuar){
					alerta("info","Articulo eliminado");
					_this.remove();
				}else{
					alerta("error",r.info);
				}
			}
		});
	}
}
</script>