<?php session_start();
$empresaid=$_SESSION["id_empresa"];
$userid=$_SESSION["id_usuario"];
header("Content-type: Application/json");

include("datos.php");
include("pivotes.php");
include("ocupa_user.php");
include("func_guardar.php");

switch($_POST["metodo"]){
		
	case 'pivote':
	//en el caso de que sea nuevo hay un control booleano para guardar primero el de la tabla piloto y luego
	//las siguientes tablas
	
	//strings pivote
	$primero=true;
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
				$campos="";
				$values="";
				
			//si el id de referencia está asignado y no está vacio entonces asigna una vez el campo y clave id_{tabla principla en singular}
				if($id!="" and !$primero){
					$campos.= $campoPivote.",";
					$values.= "'".$id."',";
				}
				
			//añade el id_usuario y el id_empresa a la primera consulta
				if($primero){
					if($ocupaUser[$tabla]){
						$campos.="id_empresa, id_usuario,";
						$values.="$empresaid, $userid,";
					}else{
						$campos.="id_empresa,";
						$values.="$empresaid,";
					}
				}
				
			//Checa si no existe una misma clave. En este script siempre habrá un primer form con la clave del registro
				if($primero){
					$clave=$datos["clave"];
					$campoPivote=$pivotes[$tabla];
					$sql=$sql="SELECT $campoPivote FROM $tabla WHERE id_empresa=$empresaid AND clave='$clave';";
					if($ocupaUser[$tabla]){
						$sql="SELECT $campoPivote FROM $tabla WHERE id_usuario=$userid AND id_empresa=$empresaid AND clave='$clave';";
					}
					$res=$bd->query($sql);
					/*if($res->rowCount()>0){
						$r["continuar"]=false;
						$r["info"]="Ya existe esta clave para el usuario $id";
						echo json_encode($r);
						exit;
					}//*/
				}
				
			//Hace el string de sql
				foreach($datos as $campo => $valor){
					if($primero and $campo=="clave"){
						$clave=$valor;
					}
					$campos.= $campo.",";
					$values.= "'".$valor."',"; //numerico no lleva ''
				}
				//generar el update on duplicate key
				$gpo="";
				foreach($datos as $campo => $valor){
					$gpo.="$campo='$valor',";
				}
				$gpo="ON DUPLICATE KEY UPDATE ".trim($gpo,",");
				
				$campos=trim($campos,",");
				$values=substr($values, 0, -1);
				$sql="INSERT INTO $tabla ($campos) VALUES ($values) $gpo;";
				//echo "$sql \n";
				
			//corre la consulta
				$res=$bd->query($sql);
				
				if($primero){		
				//corre la consulta para obtener el dato pivote id
					$campoPivote=$pivotes[$tabla];
					//echo "SELECT $campoPivote FROM $tabla WHERE id_usuario=$userid AND clave='$clave';";
					$sql="SELECT $campoPivote FROM $tabla WHERE id_empresa=$empresaid AND clave='$clave';";
					if($ocupaUser[$tabla]){
						$sql="SELECT $campoPivote FROM $tabla WHERE id_usuario=$userid AND id_empresa=$empresaid AND clave='$clave';";
					}
					$res=$bd->query($sql);
					$res=$res->fetchAll(PDO::FETCH_ASSOC);
					$id=$res[0][$campoPivote];//*/
					$primero=false;
				}
			}
			$r["continuar"]=true;
			$r["info"]="Registro añadido satisfactoriamente";
		}catch(PDOException $err){
			$r["continuar"]=false;
			$r["info"]="Error encontrado: ".$err->getMessage()."\n<br />$sql";
		}//*/
	break;
	
	
	//metodo para individual
	case 'individual':
		//asigna el nombre de la tabla
		$tabla=str_replace("f_","",$_POST["tabla"]);
		
		//ajusta los datos de los formularios
		$datos=$_POST["datos"];
		$campos="id_empresa,";
		$values="$empresaid,";
		foreach($datos as $ind=>$val){
			$campos.="$ind,";
			$values.="'$val',";
		}
		$campos=trim($campos,",");
		$values=substr($values, 0, -1);
		
		$sqlInsert="INSERT INTO $tabla ($campos) VALUES ($values);";
		try{
			$bd=new PDO($dsnw, $userw, $passw, $optPDO);
			
			//Checa si la clave ya ha sido registrada
			if(isset($datos["clave"])){
				$clave=$datos["clave"];
				$sql="SELECT * FROM $tabla WHERE id_empresa=$empresaid AND clave='$clave';";
				$res=$bd->query($sql);
				if($res->rowCount()>0){
					$r["continuar"]=false;
					$r["info"]="Error encontrado:<br />La clave: $clave, ya existe";
					echo json_encode($r);
					exit;
				}
			}
			
			//guarda el registro
			$bd->query($sqlInsert);
			$r["continuar"]=true;
			$r["info"]="Nuevo registro en $tabla añadido satisfactoriamente";
		}catch(PDOException $err){
			$r["continuar"]=false;
			$r["info"]="Error encontrado: ".$err->getMessage()."<br />".$sql;
		}
	break;
	
	//default que no hay ningun metodo
	default:
		var_dump($_POST);
	break;

}

echo json_encode($r);

?>