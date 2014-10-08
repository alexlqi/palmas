<?php session_start();
$empresaid=$_SESSION["id_empresa"];
$userid=$_SESSION["id_usuario"];
header("Content-type: Application/json");

include("datos.php");
include("pivotes.php");
include("func_guardar.php");

//en el caso de que sea nuevo hay un control booleano para guardar primero el de la tabla piloto y luego
//las siguientes tablas
$clave="";
$id="";
$campoPivote="";

//ajusta los datos de los formularios
$paquete=array();
foreach($_POST["datos"] as $form=>$datos){
	foreach($datos as $item=>$valor){
		$paquete[$form][$valor["name"]]=$valor["value"];
		if($valor["name"]=="fecha"){
				$paquete[$form][$valor["name"]]=fixFecha($valor["value"]);
			}
			if($valor["name"]=="fechaevento"){
				$paquete[$form][$valor["name"]]=fixFecha($valor["value"]);
			}
			if($valor["name"]=="fechamontaje"){
				$paquete[$form][$valor["name"]]=fixFecha($valor["value"]);
			}
			if($valor["name"]=="fechadesmont"){
				$paquete[$form][$valor["name"]]=fixFecha($valor["value"]);
			}
	}
}
unset($_POST["datos"]);
	try{
		$bd=new PDO($dsnw, $userw, $passw, $optPDO);
	
		foreach($paquete as $form=>$datos){
		//asigna el nombre de la tabla
			$tabla=str_replace("f_","",$form);
			//echo $tabla."\n";
			
		//crea el sql query
			$set="";
			if(isset($datos["id"])){
				$where="id=".$datos["id"];
			}else if(isset($datos[$pivotes[$tabla]])){
				$where=$pivotes[$tabla]."=".$datos[$pivotes[$tabla]];
			}
		//Hace el string de sql
			foreach($datos as $campo => $valor){
				$set.="$campo='$valor',";
			}
			$set=trim($set,",");
			$sql="UPDATE $tabla SET $set WHERE $where;";
			//echo "$sql \n";
			
		//corre la consulta
			$res=$bd->query($sql);
		}
		$r["continuar"]=true;
		$r["info"]="Registro guardado satisfactoriamente";
	}catch(PDOException $err){
		$r["continuar"]=false;
		$r["info"]="$campoPivote Error encontrado: ".$err->getMessage().$sql;
	}//*/
echo json_encode($r);

?>