<?php include("partes/header.php"); 
include("scripts/funciones.php"); 
?>
<script src="js/bancos.js"></script>
<div id="contenido">
    <div class="formularios">
        <h3 class="titulo_form">Listado de Bancos</h3>
        <table width="100%">
        <?php
        try{
            $sql="SELECT * FROM bancos WHERE id_empresa=$empresaid;";
            $bd=new PDO($dsnw,$userw,$passw,$optPDO);
            $res=$bd->query($sql);
            $tabla="<tr>
                <th>Banco</th>
                <th>Cuenta</th>
                <th>Clabe</th>
                <th>Acciones</th>
            </tr>";
			$bancos=array();
            foreach($res->fetchAll(PDO::FETCH_ASSOC) as $d){
                $tabla.='<tr>';
                $tabla.='<td>'.$d["nombre"].'</td>';
                $tabla.='<td>'.$d["cuenta"].'</td>';
                $tabla.='<td>'.$d["clabe"].'</td>';
                $tabla.='<td style="width:200px;">';
                $tabla.='<input type="button" value="Edo cuenta" onClick="edocuenta(this);" data-id="#banco'.$d["id_banco"].'" />';
                $tabla.='</td>';
                $tabla.='</tr>';
				$bancos[$d["id_banco"]]=$d;
            }
            echo $tabla;
        }catch(PDOException $err){
            echo "Error: ".$err->getMessage();
        }
        ?>
        </table>
    </div>
  <?php foreach($bancos as $i=>$d){ ?>
    <table id="banco<?php echo $i; ?>" class="edocuenta" style="display:none;">
    	<tr>
        	<td colspan="10"><h1>Estado de Cuenta</h1></td>
        </tr>
    	<tr>
        	<th>Nombre de banco</th>
            <th>Cuenta</th>
            <th>Clave</th>
        </tr>
        <tr>
        	<td><?php echo $d["nombre"] ?></td>
            <td><?php echo $d["cuenta"] ?></td>
            <td><?php echo $d["clabe"] ?></td>
        </tr>
        <?php //aquí van los movimientos del banco 
			try{
				$banco=$d["id_banco"];
				$mov=array();
				$sql="SELECT * FROM bancos_movimientos WHERE id_empresa=$empresaid AND id_banco=$banco;";
				$res=$bd->query($sql);
				foreach($res->fetchAll(PDO::FETCH_ASSOC) as $dd){
					$id_mov=$dd["id_movimiento"];
					unset($dd["id_movimiento"]);
					$mov[$id_mov]=$dd;
				}
			}catch(PDOException $err){
				echo $err->getMessage();
			}
			$bd=NULL;
		?>
        <tr>
        	<td colspan="10"><?php var_dump($mov); ?></td>
        </tr>
    </table>
    <?php } ?>
</div>
<?php include("partes/footer.php"); ?>