<?php include("partes/header.php");
setlocale(LC_ALL,"");
setlocale(LC_TIME,"es_MX");
include("scripts/func_form.php");

//pendientes
//- Añadir 
//- cuando se añade un nuevo articulo pasarlo al almacen

//modificación para otro cliente
//- tapar el movimiento del evento si el salón ya estpa ocupado en la nueva fecha


?>
<script src="js/cotizaciones.js"></script>
<script src="js/formularios.js"></script>
<style>
/* estilos para formularios */
.alejar_izq{
	margin-left:10px;
}
.clave{
	text-align:right;
}
.campo_form{
	margin:4px 0;
	text-align:center;
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
.label_width{
	width:175px;
}
.borrar_fecha{
	cursor:pointer;
	display:none;
}
table{
	margin:0 auto;
}
.guardar_articulo{
	background: white url('img/check.png') left center no-repeat;
	background-size:contain;
	cursor:pointer;
	width:20px;
	height:20px;
	display:inline-block;
	margin-right:10px;
}
.eliminar_articulo{
	background: white url('img/cruz.png') left center no-repeat;
	background-size:contain;
	cursor:pointer;
	width:20px;
	height:20px;
	display:inline-block;
	margin-right:10px;
}
.crearevento{
	background-color:#070;
	color:#FFF;
	font-weight:bold;
	border:none;
	cursor:pointer;
	padding: 2px 10px;
}
.crearevento:active{
	background-color:#FFF;
	color:#070;
}
#hacer .precio{
	/*display:none;*/
	width:50px;
}

.divplazos, .divbancos{
	display:inline-block;
}
#cuenta .campo_form{
	text-align:left;
}
#cuenta label{
	display:inline-block;
	width:100px;
	margin-right:5px;
}
</style>
<div id="contenido">
<div id="tabs">
  <ul>
    <li class="hacer"><a href="#hacer">Cotizar</a></li>
    <li class="mias"><a href="#mias">Mis cotizaciones</a></li>
  </ul>
  <div id="hacer">
    <form id='cotizaciones' class='formularios'>
	<h3 class='titulo_form'>Datos del evento</h3>
      <div class="tabla">
      
    <?php //si viene con una clave dedde otra pagina
	  if(isset($_GET["cve"])){?>
        <input type="hidden" name="id_cotizacion" class="id_cotizacion" value="<?php echo $_GET["cve"]; ?>" />
    <?php }else{ ?>
        <input type="hidden" name="id_cotizacion" class="id_cotizacion" value="" />
    <?php } ?>
    
        <input type="text" name="id_usuario" class="id_usuario" value="<?php echo $_SESSION["id_usuario"]; ?>" style="display:none;" />
        <input type="hidden" name="id_cliente" class="id_cliente requerido" value="" />
        
        <div class="campo_form celda">
			<label class="">CLAVE</label>
            <?php //si viene con una clave dedde otra pagina
				if(isset($_GET["cve"])){?>
				<script>
				$(document).ready(function(e){
					buscarClaveGet();
				});
				</script>
					<input type="text" name="clave" class="clave label clave_cotizacion requerido mayuscula text_corto" data-nueva="<?php nuevaClaveCotizar(); ?>" value="<?php echo $_GET["cve"]; ?>" />
			<?php }else{ ?>
				 <input type="text" name="clave" class="clave label clave_cotizacion requerido mayuscula text_corto" data-nueva="<?php nuevaClaveCotizar(); ?>" value="<?php nuevaClaveCotizar(); ?>" />
			<?php } ?>
          </div>
        <div class="campo_form celda fondo_azul" align="center">
        	<label>Salón</label><input class="eventosalon salonr" type="radio" name="quitar" value="salon" />
            <label>Evento</label><input class="eventosalon eventor" type="radio" name="quitar" value="evento" />
            <input type="hidden" class="eventosalon_h" name="eventosalon" />
        </div>
        <div class="campo_form salones celda" style="width:292px;">
			<label>Salones</label>
			<select name="salon" class="salon">
            	<option selected disabled>Elige un salón</option>
            	<?php salonesOpt();	?>
            </select>
		</div>
        <div class="campo_form celda" style="">
			<label>Tipo de evento</label>
			<select name="id_tipo" class="id_tipo">
            	<option selected disabled value="">Elige un tipo</option>
            	<?php tipoEventosOpt();	?>
            </select>
		</div>
      </div>
      <div class="tabla">
        <div class="celda" style=" width:600px;">
          <div class="campo_form">
          	<label>Nombre del cliente</label>
            <input class="cliente_cotizacion text_largo" type="text" />
          </div>
          <div class="campo_form">
            <label class="">Nombre del evento</label>
			<input type="text" name="nombre" class="nombre text_largo requerido" />
          </div>
		</div>
        <div class="celda" style="">
          <div class="campo_form">
            <label class="align_right" style="width:120px;">Fecha del evento</label>
        	<abbr title=""><input placeholder="Click para elegir" class="fecha alejar_izq requerido fechaevento" type="text" name="fechaevento" readonly/></abbr><!--
            --><img class="borrar_fecha" data-class="fechaevento" src="img/cruz.png" width="15" />
          </div>
          <div class="campo_form">
            <label class="align_right" style="width:120px;">Fecha de montaje</label>
        	<abbr title=""><input placeholder="Click para elegir" class="fecha alejar_izq requerido fechamontaje" type="text" name="fechamontaje" readonly/></abbr><!--
            --><img class="borrar_fecha" data-class="fechamontaje" src="img/cruz.png" width="15" />
          </div>
          <div class="campo_form">
            <label class="align_right" style="width:120px;">Fecha desmontaje</label>
        	<abbr title=""><input placeholder="Click para elegir" class="fecha alejar_izq requerido fechadesmont" type="text" name="fechadesmont" readonly/></abbr><!--
            --><img class="borrar_fecha" data-class="fechadesmont" src="img/cruz.png" width="15" />
          </div>
		</div>
      </div>
        <div align="right">
            <input type="button" class="modificar" value="MODIFICAR" data-wrap="#hacer" style="display:none;" />
            <input type="button" class="guardar" value="CREAR" data-wrap="#hacer" data-accion="guardar" data-m="pivote" />
            <input type="button" class="nueva" value="NUEVA" data-s="s_nueva_cot" />
        </div>
	</form>
    <div class='formularios'>
	<h3 class='titulo_form'>Artículos y Paquetes</h3>
    <table id="articulos">
      <tr>
      	<th class="agregar_articulo"><img src="img/mas.png" height="25" /></th>
        <th width="100">Cant.</th>
        <th width="250">Concepto</th>
        <th width="100">precio unitario</th>
        <th width="100">total</th>
        <th width="150">Acciones</th>
      </tr>
    </table>
    </div>
    <!--
    <div class="formularios">
        <h3 class='titulo_form'>Otros conceptos</h3>
        <table id="otros">
          <tr>
            <th class="agregar_otros"><img src="img/mas.png" height="25" /></th>
            <th width="100">Cant.</th>
            <th width="250">Concepto</th>
            <th width="100">precio unitario</th>
            <th width="100">total</th>
            <th width="150">Acciones</th>
          </tr>
        </table>
    </div>
    -->
    <div id="cuenta" class="formularios" align="left">
    <h3 class='titulo_form'>Cuenta</h3>
        <div class="campo_form">
            <label class="">Total del evento</label>
            <input type="text" class="totalevento numerico" />
        </div>
        <div class="campo_form">
            <label class="">Metodo de pago</label>
            <select class="metodo">
            	<option value="contado">De contado</option>
                <option value="credito">A crédito</option>
                <option value="cheque">Cheque</option>
                <option value="transferencia">Transferencia</option>
            </select>
            <div class="divplazos" style="display:none;">
                <label class="">Plazos:</label>
                <input type="text" class="plazos numerico" size="4" value="1" />
            </div>
            <div class="divbancos" style="display:none;">
                <label class="">Bancos:</label>
                <select class="bancos"><option>Elige un banco</option></select>
            </div>
        </div>
        <div class="campo_form">
            <label class="">Anticipo:</label>
            <input type="text" class="anticipo numerico" />
            <label class="">Restante:</label>
            <input type="text" class="restante numerico" readonly="readonly" />
        </div>
        <div align="right">
        	<a id="imp_cot" href="" target="_blank">Imprimir cotización</a>
            <input type="button" class="crearevento" value="Pasar a evento" onclick="pasarevento();" />
        </div>
    </div>
  </div>
  <!-- //sección de las cotizaciones por empresa y or usuario --> 
  <div id="mias">
  <style>
  	#mias table{
		font-size:0.85em;
	}
	#mias th{
		font-size:1.05em;
		margin:2px;
	}
	#mias td{
		margin:2px;
		padding:5px 2px;
	}
	#mias .filtro{
		width:100%;
	}
	.accion{
		margin:0 5px;
		cursor:pointer;
	}
  </style>
  <table cellpadding="0" cellspacing="2" border="0" width="100%" class="listado">
  <tr>
  	<th>Clave<br />Folio</th>
    <th style="width:200px;">Nombre del evento</th>
    <th>Tipo de evento</th>
    <th style="width:200px;">Cliente</th>
    <th>Estatus</th>
    <th>Fecha<br />evento</th>
    <th>Montaje</th>
    <th>Desmontaje</th>
    <th>acciones</th>
  </tr>
  <tr class="barra_accion">
    <td style="width:34px;"><input class="filtro" data-c="bfolio" /></td>
    <td><input class="filtro" data-c="bnombre" /></td>
    <td><input class="filtro" data-c="btipo_evento" /></td>
    <td><input class="filtro" data-c="bcliente" /></td>
    <td style="width:34px;"><input class="filtro" data-c="bestatus" /></td>
    <td><input class="filtro filtrofecha" data-c="bfechaevento" /></td>
    <td><input class="filtro filtrofecha" data-c="bfechamontaje" /></td>
    <td><input class="filtro filtrofecha" data-c="bfechadesmont" /></td>
    <td><a href="#" class="pdf" onclick="return false;" data-nombre="evento" data-orientar="L">generar pdf</a></td>
  </tr>
  	<?php 
	try{
		$bd=new PDO($dsnw,$userw, $passw, $optPDO);
		$sqlCot="SELECT
			id_cotizacion,
			cotizaciones.clave,
			cotizaciones.nombre,
			tipo_evento.nombre as tipo_evento,
			estatus,
			fechaevento,
			fechamontaje,
			fechadesmont
		FROM cotizaciones
		INNER JOIN tipo_evento ON cotizaciones.id_tipo=tipo_evento.id_tipo
		WHERE cotizaciones.id_empresa=$empresaid AND id_usuario=$userid;";
		$sqlClie="SELECT
			id_cotizacion,
			clientes.id_cliente,
			clientes.nombre,
			clientes.limitecredito
		FROM clientes
		INNER JOIN cotizaciones ON clientes.id_cliente = cotizaciones.id_cliente
		WHERE clientes.id_empresa=$empresaid;";
		
		$cot=array();
		$res=$bd->query($sqlCot);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			$ind=$v["id_cotizacion"];
			unset($v["id_cotizacion"]);
			$cot[$ind]=$v;
		}
		
		$cli=array();
		$res=$bd->query($sqlClie);
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			$ind=$v["id_cotizacion"];
			unset($v["id_cotizacion"]);
			$cli[$ind]=$v;
		}
		
		
		//correlacionar los subarrays al array principal de cotizacion
		foreach($cot as $ind=>$val){
			$cot[$ind]["cliente"]=$cli[$ind]["nombre"];
		}
		
		//escribimos la tabla
		foreach($cot as $folio=>$d){
			echo '<tr class="cot'.$d["clave"].'">';
			echo '<td class="bfolio">'.$d["clave"].'</td>';
			echo '<td class="bnombre">'.$d["nombre"].'</td>';
			echo '<td class="btipo_evento">'.$d["tipo_evento"].'</td>';
			echo '<td class="bcliente">'.$d["cliente"].'</td>';
			echo '<td class="bestatus">'.$d["estatus"].'</td>';
			echo '<td class="bfechaevento">'.varFechaAbrNorm($d["fechaevento"]).'</td>';
			echo '<td class="bfechamontaje">'.varFechaAbrNorm($d["fechamontaje"]).'</td>';
			echo '<td class="bfechadesmont">'.varFechaAbrNorm($d["fechadesmont"]).'</td>';
			echo '<td><img class="accion" src="img/edit.png" data-cve="'.$d["clave"].'" data-id="'.$folio.'" onclick="editar(this);" height="20" /><img class="accion eliminar" src="img/cruz.png" data-cve="'.$folio.'" height="20" /></td>';
			echo '</tr>';
		}
		$bd=NULL;
	}catch(PDOException $err){
		echo "Error encontrado: ".$err->getMessage();
	}
	?>
  	</table>
  </div>
</div>
</div>
<?php include("partes/footer.php"); ?>