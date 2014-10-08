<?php session_start();
include("datos.php");
include("funciones.php");

$id=$_POST["id"];
try{
    $bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sql="SELECT 
		familias.id_familia,
		familias.nombre
	FROM familias
	INNER JOIN afs ON afs.id_familia=familias.id_familia
	WHERE familias.id_empresa=".varEmpresa()." AND afs.id_area=$id
	GROUP BY familias.id_familia;";
    $res=$bd->query($sql);
    echo '<option selected="selected" disabled="disabled">Elige familia</option>';
    foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
        echo '<option value="'.$v["id_familia"].'">'.$v["nombre"].'</option>';
    }
}catch(PDOException $err){
    echo "<option>Error: ".$err->getMessage();
}
?>