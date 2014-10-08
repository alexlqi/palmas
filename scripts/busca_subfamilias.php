<?php session_start();
include("datos.php");
include("funciones.php");

$id=$_POST["id"];
try{
    $bd=new PDO($dsnw, $userw, $passw, $optPDO);
	$sql="SELECT 
		subfamilias.id_subfamilia,
		subfamilias.nombre
	FROM subfamilias
	INNER JOIN afs ON afs.id_subfamilia=subfamilias.id_subfamilia
	WHERE subfamilias.id_empresa=".varEmpresa()." AND afs.id_familia=$id
	GROUP BY subfamilias.id_subfamilia;";
    $res=$bd->query($sql);
    echo '<option selected="selected" disabled="disabled">Elige subfamilia</option>';
    foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
        echo '<option value="'.$v["id_subfamilia"].'">'.$v["nombre"].'</option>';
    }
}catch(PDOException $err){
    echo "<option>Error: ".$err->getMessage();
}
?>