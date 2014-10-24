<?php session_start();
include("datos.php");
include("funciones.php");

//1.- primero actualiza el inventario
actInv($dsnw,$userw,$passw,$optPDO);

?>
<style>
</style>

<div class="apdf">
<h2>Listado de inventario Actual</h2>

<table>
<tr>
	<th>Nombre Artículo</th>
    <th>Unidades</th>
    <th>Totales</th>
    <th>Cantidad actual</th>
</tr>
<?php //para llenar la tabla
$empresa=$_SESSION["id_empresa"];

try{
	$bd=new PDO($dsnw,$userw,$passw,$optPDO);
	$sql="SELECT 
		almacen.id_item,
		articulos.id_articulo,
		articulos.nombre,
		articulos.unidades,
		almacen.cantidad as totales,
		almacen_inventario.cantidad as cantidad
	FROM almacen_inventario
	LEFT JOIN almacen ON almacen_inventario.id_item = almacen.id_item
	INNER JOIN articulos ON almacen_inventario.id_articulo = articulos.id_articulo
	WHERE almacen_inventario.id_empresa=$empresa;";
	$res=$bd->query($sql);
	foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
		echo '<tr>';
		echo '<td>'.$v["nombre"].'</td>';
		echo '<td>'.$v["unidades"].'</td>';
		echo '<td width="200">'.$v["totales"].'</td>';
		echo '<td width="200">'.$v["cantidad"].'</td>';
		echo '</tr>';
	}
}catch(PDOException $err){
	echo 'Error Encontrado: '.$err->getMessage();
}
?>
</table>
</div>