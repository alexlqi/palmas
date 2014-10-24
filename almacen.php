<?php include("partes/header.php");
include("scripts/funciones.php");
include("scripts/func_form.php");
setlocale(LC_ALL,'');
setlocale(LC_ALL,'es_MX');

actInv($dsnw,$userw,$passw,$optPDO);
//pendientes
//- poner un formulario por articulos no devueltos

//checar la integridad de los articulos y el almacén
	//1.- insertar los articulos en el almacen y el inventario
	
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sqlAlm="INSERT INTO almacen (id_empresa, id_articulo, cantidad)
	SELECT id_empresa, id_articulo, 0 FROM articulos 
	WHERE id_empresa=$empresaid AND articulos.id_articulo NOT IN (SELECT id_articulo FROM almacen WHERE id_empresa=$empresaid);";
	$sqlInv="INSERT INTO almacen_inventario (id_empresa, id_articulo, cantidad)
	SELECT id_empresa, id_articulo, 0 FROM articulos
	WHERE id_empresa=$empresaid AND articulos.id_articulo NOT IN (SELECT id_articulo FROM almacen_inventario WHERE id_empresa=$empresaid);";
	$bd->query($sqlAlm);
	$bd->query($sqlInv);
}catch(PDOException $err){
	echo $err="Error encontrado: ".$err->getMessage();
}
$today=date("Y-m-d H:i:s");
$inicioMes=date("Y-m-d H:i:s", mktime(0,0,0,date("n")-1,1,date("Y")));
$finMes=date("Y-m-d H:i:s", mktime(23,59,59,date("n")+1,0,date("Y")));

$bd=NULL;
try{
	$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	
	//obtiene el array de las areas, familias y subfamilias para los articulos (por id_articulo)
	$afs_art=array();
	$sqlAfs="SELECT 
		id_articulo,
		articulos.nombre as articulo,
		areas.nombre as area,
		familias.nombre as familia,
		subfamilias.nombre as subfamilia
	FROM articulos
	LEFT JOIN areas ON articulos.area=areas.id_area
	LEFT JOIN familias ON articulos.familia=familias.id_familia
	LEFT JOIN subfamilias ON articulos.subfamilia=subfamilias.id_subfamilia
	WHERE articulos.id_empresa=$empresaid;";
	$res=$bd->query($sqlAfs);
	
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$id=$v["id_articulo"];
		unset($v["id_articulo"]);
		$afs_art[$id]=$v;
	}
	//<-------no se mueve
	
	//Checar lo disponible de cada articulo en el almacen_inventario
	//Para el listado de eventos
	$inventarioCant=array();
	$sqlInventario="SELECT
		articulos.id_articulo,
		articulos.nombre,
		articulos.unidades,
		cantidad
	FROM almacen_inventario
	INNER JOIN articulos ON almacen_inventario.id_articulo=articulos.id_articulo
	WHERE articulos.id_empresa=$empresaid;";
	$res=$bd->query($sqlInventario);
	
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$id=$v["id_articulo"];
		$inventarioCant["nombre"][$id]=$v["nombre"];
		$inventarioCant["unidades"][$id]=$v["unidades"];
		$inventarioCant["total"][$id]=$v["cantidad"];
	}
	
	//obtiene los datos de los eventos
	$sqlEvent="SELECT 
		id_evento,
		id_cliente,
		clave,
		nombre,
		estatus,
		fechaevento,
		fechamontaje,
		fechadesmont
	FROM eventos
	WHERE id_empresa=$empresaid AND fechamontaje < '$today' AND fechadesmont < '$today';";
	$res=$bd->query($sqlEvent);
	
	//este array es para tener en una variable las entradas y saluidas del almacén
	$eventos=array();
	
	//para las entradas
	$sqlEveCheck="SELECT 
		eventos.*
	FROM eventos
	INNER JOIN almacen_entradas ON eventos.id_evento=almacen_entradas.id_evento
	WHERE eventos.id_empresa=$empresaid AND estatus=2 AND eventos.fechaevento BETWEEN '$inicioMes' AND '$finMes' AND entro=0 GROUP BY eventos.id_evento;";
	$res=$bd->query($sqlEveCheck);
	if($res->rowCount()>0){
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			$id=$v["id_evento"];
			unset($v["id_evento"]);
			$eventos["entradas"][$id]=$v;
			$obj=$bd->query("SELECT 
				*
			FROM almacen_entradas
			WHERE id_evento=$id AND id_empresa=$empresaid AND entro=0;");
			$i=0;
			foreach($obj->fetchAll(PDO::FETCH_ASSOC) as $item){
				$eventos["entradas"][$id]["items"][$i]=$item;
				$eventos["entradas"][$id]["items"][$i]["articulo"]=$afs_art[$item["id_articulo"]]["articulo"];
				$eventos["entradas"][$id]["items"][$i]["area"]=$afs_art[$item["id_articulo"]]["area"];
				$eventos["entradas"][$id]["items"][$i]["familia"]=$afs_art[$item["id_articulo"]]["familia"];
				$eventos["entradas"][$id]["items"][$i]["subfamilia"]=$afs_art[$item["id_articulo"]]["subfamilia"];
				$i++;
			}
		}
	}else{
		$eventos["entradas"]=array();
	}
	
	//para las salidas
	$sqlEveCheck="SELECT 
		*
	FROM eventos
	INNER JOIN almacen_salidas ON eventos.id_evento=almacen_salidas.id_evento
	WHERE eventos.id_empresa=$empresaid AND estatus=2 AND fechaevento BETWEEN '$inicioMes' AND '$finMes' AND salio=0 GROUP BY eventos.id_evento;";
	$res=$bd->query($sqlEveCheck);
	if($res->rowCount()>0){
		foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
			$id=$v["id_evento"];
			unset($v["id_evento"]);
			$eventos["salidas"][$id]=$v;
			$obj=$bd->query("SELECT 
				almacen_salidas.*,
				articulos.unidades
			FROM almacen_salidas
			INNER JOIN articulos ON almacen_salidas.id_articulo = articulos.id_articulo
			WHERE id_evento='$id' AND almacen_salidas.id_empresa=$empresaid AND almacen_salidas.termino=0;");
			$i=0;
			foreach($obj->fetchAll(PDO::FETCH_ASSOC) as $item){
				$eventos["salidas"][$id]["items"][$i]=$item;
				$eventos["salidas"][$id]["items"][$i]["articulo"]=$afs_art[$item["id_articulo"]]["articulo"];
				$eventos["salidas"][$id]["items"][$i]["area"]=$afs_art[$item["id_articulo"]]["area"];
				$eventos["salidas"][$id]["items"][$i]["familia"]=$afs_art[$item["id_articulo"]]["familia"];
				$eventos["salidas"][$id]["items"][$i]["subfamilia"]=$afs_art[$item["id_articulo"]]["subfamilia"];
				$i++;
			}
		}
	}else{
		//por si no hay ningun movimiento
		$eventos["salidas"]=array();
	}
	
}catch(PDOException $err){
	echo $err->getMessage();
}

//control para que no vean la sección los de ventas y coordinadores solo almacenistas y administradores
if($_SESSION["categoria"]!="ventas" and $_SESSION["categoria"]!="coordinador"){
?>
<script src="js/almacen.js"></script>
<script src="js/formularios.js"></script>
<style>
table{
	width:100%;
	background-color:#F1F1F1;
}
th{
	width:14.28%;
}
td{
	background-color:#FFF;
	margin:1px;
	width:14.28%;
}
.item_agenda{
	background-color:rgba(0,182,255,0.8);
	color:#FFF;
	padding:3px 2px;
	margin-bottom:2px;
	cursor:pointer;
}
.hover{
	display:none;
	position:absolute;
	top:0;
	left:0;
	width:100%;
	height:100%;
	background-color:rgba(0,0,0,0.3);
	z-index:20;
	cursor:pointer;
}
.area_cont{
	cursor:default;
	width:75%;
	height:75%;
	position:absolute;
	top:25%;
	left:25%;
	margin-top:-7.5%;
	margin-left:-12.5%;
	background-color:#FFF;
	overflow:auto;
}
.checar{
	cursor:pointer;
}
</style>
<div id="contenido">
<div id="tabs">
  <ul>
  	<li><a href="#semana">Eventos</a></li>
    <li><a href="#inventario">Inventario</a></li>
    <li><a href="#entradas">Entradas</a></li>
    <li><a href="#salidas">Salidas</a></li>
    <li><a href="#tickets">Tickets</a></li>
  </ul>
  <div id="semana">
  	<div class="hover">
    	<div class="area_cont">
        	<div class="apdf">
            	<style>
					.titulo_pdf{
						font-size:32px;
						font-weight:bold;
					}
                </style>
            	<page>
					<div class='titulo_pdf' align="center">Lista de articulos para evento</div>
                    <div class="datos_evento_pdf">Datos del evento para mostrar en el reporte</div>
                    <div class="articulos_pdf">Listado de articulos de almacen</div>
		        </page>
            </div>
        <a href="#" class="pdf" onclick="return false;" data-nombre="evento" data-orientar="P">generar pdf</a>
        </div>
    </div>
  	
    <!-- Aquí van los controles para el calendario -->
        <div id="control_calendario" class="tabla" align="center">
          <div class="celda">
            <div id="mes_previo" class="mover_calendario" data-m="" data-a=""></div>
            <div id="mesanio" data-m="<?php esteMes(); ?>" data-a="<?php esteAnio(); ?>"></div>
            <div id="mes_siguiente" class="mover_calendario" data-m="" data-a=""></div>
          </div>
          <div id="filtro_tipo" class="celda">
          	  <div align="center">FILTROS</div>
              <label>Documento:</label><select id="tipo_proyecto"><!--
                  --><option selected="selected">Selecciona</option>
                  <option value="ambos">Ambos</option>
                  <option value="cotizacion">Cotizaciones</option>
                  <option value="event">Eventos</option>
              </select>
              <label>Tipo:</label><select id="tipo_evento"><!--
                  --><option selected="selected">Selecciona</option>
                  <option value="todos">Todos</option>
                  <?php tipoEventosOpt(); ?>
              </select>
          </div>
          <div id="estatus" class="celda">
          </div>
        </div>
        <div id="calendario" class="tabla" align="center">
        <!-- Aquí va la tabla del mes con divs -->
          <div id="dias" class="fila fondo_azul">
            <div class="celda">DOMINGO</div>
            <div class="celda">LUNES</div>
            <div class="celda">MARTES</div>
            <div class="celda">MIÉRCOLES</div>
            <div class="celda">JUEVES</div>
            <div class="celda">VIERNES</div>
            <div class="celda">SÁBADO</div>
          </div>
          <div id="semana1" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana2" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana3" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana4" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana5" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana6" class="semana fila fondo_gris" style="display:none;">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
        </div>
    
    
    
    <!--<table cellpadding="0" cellpadding="0" border="0">
    	<tr>
        	<?php foreach($dias as $v){
				echo '<th>'.$v["dia"].'<br>'.$v["fecha"].'</th>';
			}?>
        </tr>
        <tr height="400" valign="top" align="center">
        	<?php foreach($dias as $i=>$v){
				echo '<td class="'.$i.'">';
				if(isset($eventos[$i])){
				echo '<div class="item_agenda">'.$eventos[$i]["nombre"].'</div>';
				}
				echo '</td>';
			}?>
            	
        </tr>
    </table>-->
  </div>
  
  <!-- Listado para poner los registros del inventario -->
  <div id="inventario">
  	
  </div>
<!-- ENTRADAS A INVENTARIO -->
  <div id="entradas">
  	<form id="entradas_form" class="formularios">
    	<h3 class="titulo_form">Alta de artículos en almacén</h3>
    	<input type="hidden" name="id_empresa" value="<?php empresa(); ?>" />
    	<label class="">Artículo:</label><select class="id_articulo" name="id_articulo">
        	<option selected="selected" data-unidades="" value="">Elige una opcion</option>
            <?php //aqui se ponen los articulos 
			$unidades=array();
			foreach($inventarioCant["unidades"] as $id=>$v){
				$unidades[$id]=$v;
			}
			foreach($inventarioCant["nombre"] as $id=>$v){
				echo '<option value="'.$id.'" data-unidades="'.$unidades[$id].'">'.$v.'</option>';
			}
			?>
        </select>
        <label class="">Cantidad:</label><input type="text" class="cantidad" name="cantidad" /><label class="unidades"></label>
        <input type="button" class="b_entrada" data-id="#entradas_form" value="guardar" />
    </form>
    <div class="formularios" style="height:300px; overflow:auto;">
    	<h3 class="titulo_form" style="margin-top:15px;">Checklist de artículos entregados</h3>
        <table style="margin-top:15px;">
            <tr>
            	<th>Evento</th>
                <th>Fecha evento</th>
                <th>Acciones</th>
            </tr>
			<?php //escribir la lista de los eventos 
            $formas="";
            foreach($eventos["entradas"] as $ind=>$v){
                echo '<tr>';
                echo '<td>'.$v["nombre"].'</td>';
                echo '<td>'.varFechaAbreviada($v["fechaevento"]).'</td>';
                echo '<td>';
                if(strtotime($today)>=strtotime($v["fechadesmont"])){
                    echo '<abbr title="Ver Detalle"><img src="img/lista.png" height="30" class="checar" data-list="lista'.$ind.'" /></abbr>';
                }else{
                    echo '<strong>Disponible hasta: '.varFechaAbreviada($v["fechadesmont"]).'</strong>';
                }
                echo '</td>';
                echo '</tr>';
                //generar la tabla para mostrar los articulos a sacar
                $tabla='<table style="margin:5px auto;">';
                $tabla.="<tr><th>ÁREA</th><th>FAMILIA</th><th>SUBFAMILIA</th><th>ARTICULO</th><th>CANTIDAD</th><th>REGRESARON</th></tr>";
                if(isset($v["items"])){
                    foreach($v["items"] as $d){
                        $tabla.='<tr>';
                        $tabla.='<td>'.$d["area"].'</td>';
                        $tabla.='<td>'.$d["familia"].'</td>';
                        $tabla.='<td>'.$d["subfamilia"].'</td>';
                        $tabla.='<td>'.$d["articulo"].'</td>';
                        $tabla.='<td class="cotejar">'.($d["cantidad"]-$d["regresaron"]).'</td>';
                        $tabla.='<td><input type="text" size="6" class="numerico" data-max="'.($d["cantidad"]-$d["regresaron"]).'" data-regresaron="'.$d["regresaron"].'" data-evento="'.$ind.'" data-art="'.$d["id_articulo"].'" value="0" /></td>';
                        $tabla.='</tr>';
                    }
                }
                $tabla.="</table>";
                $tabla.='<div align="right"><input type="button" value="Reingresar" data-evento="'.$ind.'" class="reingresar"  /></div>';
                
                $formas.='<div style="display:none;" class="listas lista'.$ind.'">'.$tabla.'</div>';
            }
            ?>
        </table>
    </div>
    <?php echo $formas; ?>
  </div>
  
<!-- SALIDAS -->
  <div id="salidas">
  	<form id="salidas_form" class="formularios">
        <h3 class="titulo_form">Baja de artículos en almacén</h3>
        <input type="hidden" name="id_empresa" value="<?php empresa(); ?>" />
        <label class="">Artículo:</label><select class="id_articulo" name="id_articulo">
            <option selected="selected" value="">Elige una opcion</option>
            <?php //aqui se ponen los articulos 
            foreach($inventarioCant["nombre"] as $id=>$v){
                echo '<option value="'.$id.'">'.$v.'</option>';
            }
            ?>
        </select>
        <label class="">Cantidad:</label><input type="text" class="cantidad" name="cantidad" />
        <input type="button" class="b_salida" data-id="#salidas_form" value="guardar" />
    </form>
    <div class="formularios">
    <h3 class="titulo_form">Checklist de artículos Por Salir</h3>
        <table>
            <tr>
            	<th>Evento</th>
                <th>Fecha evento</th>
                <th>Acciones</th>
            </tr>
            <div class="checklist">
                <?php //escribir la lista de los eventos 
				$formas="";
				foreach($eventos["salidas"] as $ind=>$v){
					echo '<tr>';
					echo '<td>'.$v["nombre"].'</td>';
					echo '<td>'.varFechaAbreviada($v["fechaevento"]).'</td>';
					echo '<td>';
					if(strtotime($today)>=strtotime($v["fechamontaje"])){
						echo '<abbr title="Ver Detalle"><img src="img/lista.png" height="30" class="checar" data-list="lista'.$ind.'" /></abbr>';
						echo '<abbr title="A PDF"><img src="img/pdf.png" height="30" class="apdf" data-list="lista'.$ind.'" data-json=\''.json_encode($eventos["salidas"][$ind]).'\' /></abbr>';
					}else{
						echo '<strong>Disponible hasta: '.varFechaAbreviada($v["fechamontaje"]).'</strong>';
					}
					echo '</td>';
					echo '</tr>';
					//generar la tabla para mostrar los articulos a sacar
					$tabla='<table style="margin:5px auto;">';
					$tabla.="<tr><th>ÁREA</th><th>FAMILIA</th><th>SUBFAMILIA</th><th>ARTICULO</th><th>DISPONIBLE</th><th>CANTIDAD</th><th>AUTORIZAR</th></tr>";
					if(isset($v["items"])){
						foreach($v["items"] as $d){
							if(($d["cantidad"]*1)>0){
								$tabla.='<tr>';
								$tabla.='<td>'.$d["area"].'</td>';
								$tabla.='<td>'.$d["familia"].'</td>';
								$tabla.='<td>'.$d["subfamilia"].'</td>';
								$tabla.='<td>'.$d["articulo"].'</td>';
								$tabla.='<td>'.$inventarioCant["total"][$d["id_articulo"]].'</td>';
								$tabla.='<td>'.$d["cantidad"].'</td>';
								$checked="";
								if($d["salio"]==1){
									$tabla.='<td><input type="checkbox" data-evento="'.$ind.'" data-art="'.$d["id_salida"].'" checked="checked" disabled="disabled" /></td>';
								}else{
									$tabla.='<td><input type="checkbox" data-evento="'.$ind.'" data-art="'.$d["id_salida"].'" '.$checked.' /></td>';
								}
								$tabla.='</tr>';
							}
						}
					}
					$tabla.="</table>";
					$tabla.='<div align="right"><input type="button" value="autorizar" data-evento="'.$ind.'" class="autorizar" /></div>';
					
					$formas.='<div style="display:none;" class="listas lista'.$ind.'">'.$tabla.'</div>';
				}
				?>
            </div>
        </table>
        <?php echo $formas; ?>
    </div>
  </div>
  <div id="tickets">
  </div>
</div>
</div>
<?php }//if para los que no son almacenistas oc oordinadores
include("partes/footer.php"); ?>