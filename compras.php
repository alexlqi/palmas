<?php include("partes/header.php"); 
include ("scripts/func_form.php");
try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	
	//para saber la compra
	$sql="SELECT 
		id_compra,
		folio,
		compras.fecha,
		compras.estatus,
		eventos.id_evento,
		eventos.nombre,
		eventos.fechamontaje,
		eventos.fechadesmont
	FROM compras
	INNER JOIN eventos ON compras.id_evento=eventos.id_evento
	WHERE compras.id_empresa=$empresaid;";
	$res=$bd->query($sql);
	$compras=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$id=$v["id_compra"];
		unset($v["id_compra"]);
		$compras[$id]=$v;
	}
	
	//para saber los articulos dentro de la orden de compra
	$sql="SELECT 
		compras_articulos.id_compra,
		compras_articulos.id_item,
		articulos.id_articulo,
		compras_articulos.cantidad,
		articulos.nombre,
		articulos.unidades,
		articulos.perece,
		listado_precios.compra
	FROM compras_articulos
	INNER JOIN articulos ON compras_articulos.id_articulo=articulos.id_articulo
	INNER JOIN listado_precios ON compras_articulos.id_articulo=listado_precios.id_articulo
	WHERE compras_articulos.id_empresa=$empresaid;";
	$res=$bd->query($sql);
	$art_comp=array();
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		$id=$v["id_compra"];
		$item=$v["id_item"];
		unset($v["id_item"],$v["id_compra"]);
		$art_comp[$id][$item]=$v;
	}
}catch(PDOException $err){
	echo "Error: ".$err->getMessage();
}

$estatus=array(
	0=>"cancelada",
	1=>"pendiente",
	2=>"pagada",
);
?>
<style>
#contenido *{
	font-size:1em;
}
table{
	width:100%;
}
</style>
<script src="js/compras.js"></script>
<div id="contenido">
	<?php foreach($compras as $id=>$v){ ?>
	<div id="wrap_forma_<?php echo $id; ?>" class="formularios formacompra" style="display:none;">
    <h3 class="titulo_form">Compra Folio No <?php echo $v["folio"]; ?></h3>
    <form id="forma<?php echo $id; ?>" class="f_compra" style="width:80%; margin: 0 auto;">
        <input type="hidden" class="id_compra" name="compra[id_compra]" value="<?php echo $id; ?>" />
        <input type="hidden" class="id_evento" name="compra[id_evento]" value="<?php echo $v["id_evento"]; ?>" />
        <input type="hidden" class="fechamontaje" name="compra[mont]" value="<?php echo $v["fechamontaje"]; ?>" />
        <input type="hidden" class="fechadesmont" name="compra[desmont]" value="<?php echo $v["fechadesmont"]; ?>" />
        <label>Método de entrada</label><select class="entrada requerido" data-campo="Metodo de entrada" name="compra[metodo]">
        	<option value="">Elige Método</option>
        	<option value="renta">Renta</option>
            <option value="compra">Compra</option>
        </select><br>
        <label>Método de pago</label><select class="pagopor requerido" data-campo="Metodo de pago" name="compra[pagopor]">
        	<option value="">Elige Método</option>
        	<option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
            <option value="tc">Tarjeta de credito</option>
            <option value="td">Tarjeta de débito</option>
        </select><br>
        <div class="bancos" style="display:none;">
            <label>Banco</label><select name="compra[banco]">
                <option value="">Elige Banco</option>
                <?php 
                    try{
                        //para saber la compra
                        $sql="SELECT * FROM bancos WHERE id_empresa=$empresaid;";
                        $res=$bd->query($sql);
                        foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
                            echo '<option value="'.$v["id_banco"].'">'.$v["nombre"].'</option>';
                        }
                    }catch(PDOException $err){
                        echo "Error: ".$err->getMessage();
                    }
    
                ?>
            </select>
        </div>
        <table>
	<?php 	//para los articulos dentro de la orden de compra
			foreach($art_comp[$id] as $item=>$vv){ ?>
    		<tr>
				<td><?php echo $vv["nombre"]; ?></td>
            	<td>
					<?php echo $vv["cantidad"]." ".$vv["unidades"]; ?>
                    <input type="hidden" name="compra[articulos][<?php echo $vv["id_articulo"]; ?>][cantidad]" value="<?php echo $vv["cantidad"]; ?>" />
                    <input type="hidden" name="compra[articulos][<?php echo $vv["id_articulo"]; ?>][perece]" value="<?php echo $vv["perece"]; ?>" />
                </td>
            	<td><label>Proveedor:</label><select class="proveedores requerido" data-campo="Proveedor de <?php echo $vv["nombre"]?>" name="compra[articulos][<?php echo $vv["id_articulo"]; ?>][proveedor]">
                    <option value="">Elige proveedor</option>
                    <?php 
                        try{
                            //para saber la compra
                            $sql="SELECT * FROM proveedores WHERE id_empresa=$empresaid;";
                            $res=$bd->query($sql);
                            foreach($res->fetchAll(PDO::FETCH_ASSOC) as $vvv){
                                echo '<option value="'.$vvv["id_proveedor"].'">'.$vvv["nombre"].'</option>';
                            }
                        }catch(PDOException $err){
                            echo "Error: ".$err->getMessage();
                        }
    
                    ?>
                </select></td>
                <td>$<input size="6" type="text" class="monto numerico" data-form="#forma<?php echo $id; ?>" name="compra[articulos][<?php echo $vv["id_articulo"]; ?>][monto]" value="<?php echo $vv["compra"]*$vv["cantidad"]; ?>" /></td>
            </tr>
    <?php } ?>
    		<tr>
            	<td></td><td></td>
                <td style="text-align:right;">Total=</td><td>$<input type="text" size="6" class="totalcompra" name="compra[totalcompra]" readonly="readonly" /></td>
            </tr>
    	</table><br>
        <input type="button" value="Hecho" class="hecho" data-form="#forma<?php echo $id; ?>" data-boton="#compra1" />
    </form>
    </div>
    <?php } ?>
	
	<div class="formularios">
    <h3 class="titulo_form">Compras</h3>
	<table style="width:100%;">
    	<tr><th>Folio</th><th>Fecha de compra</th><th>evento</th><th>estatus</th><th>acciones</th></tr>
        <?php
			if(count($compras)>0){
				foreach($compras as $i=>$v){
					echo '<tr id="compra'.$i.'">';
					echo '<td>'.$v["folio"].'</td>';
					echo '<td>'.varFechaAbreviada($v["fecha"]).'</td>';
					echo '<td>'.$v["nombre"].'</td>';
					echo '<td class="estatus">'.$estatus[$v["estatus"]].'</td>';
					if($v["estatus"]==1){
						echo '<td>
							<input type="button" class="enviar" value="Enviar" data-a="enviar" data-id="'.$i.'" onclick="enviar(this);" />
							<form action="scripts/imprimeCompra.php" method="post" target="_blank" style="display:inline-block">
							  <input type="hidden" name="compra" value="'.$i.'" />
							  <input type="submit" value="Imprimir" data-a="imprimir" data-id="'.$i.'" />
							</form>
							<input type="button" class="comprar" value="Generar entrada" data-id="'.$i.'" />
							<input type="button" class="cancelar" value="Cancelar" data-id="'.$i.'" /> 
						</td>';
					}elseif($v["estatus"]==2){
						echo '<td>';
						echo '<input type="button" class="enviar" value="Enviar" data-a="enviar" data-id="'.$i.'" onclick="enviar(this);" /> ';
						echo '<form action="scripts/imprimeCompra.php" method="post" target="_blank" style="display:inline-block">
							  <input type="hidden" name="compra" value="'.$i.'" />
							  <input type="submit" value="Imprimir" data-a="imprimir" data-id="'.$i.'" />
							</form>';
						echo '</td>';
					}else{
						echo '<td></td>';
					}
					echo '</tr>';
				}
			}else{
				echo "<tr><td colspan='4'>no hay ninguna compra</td></tr>";
			}
		?>
    </table>
    </div>
</div>
<?php include("partes/footer.php"); ?>