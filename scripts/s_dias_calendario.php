<?php 
setlocale(LC_ALL,"");
setlocale(LC_TIME,"es_MX");
date_default_timezone_set("America/Monterrey");
header("Content-type: Application/json");

if(isset($_POST["m"]) and isset($_POST["a"])){
  if($_POST["m"]!="" and $_POST["a"]!=""){
	$r["continuar"]=true;
	$r["delay"]=date("w",mktime(0,0,1,$_POST["m"],1,$_POST["a"]));
	$r["ultimo"]=date("d",mktime(0,0,0,$_POST["m"]+1,0,$_POST["a"]));
	$r["fecha"]=strtoupper(strftime("%B %Y",mktime(0,0,0,$_POST["m"]+1,0,$_POST["a"])));
	$r["sigm"]=date("n",mktime(0,0,0,$_POST["m"]+1,1,$_POST["a"]));
	$r["siga"]=date("Y",mktime(0,0,0,$_POST["m"]+1,1,$_POST["a"]));
	$r["prevm"]=date("n",mktime(0,0,0,$_POST["m"],0,$_POST["a"]));
	$r["preva"]=date("Y",mktime(0,0,0,$_POST["m"],0,$_POST["a"]));
  }else{
	$r["continuar"]=false;
	$r["info"]="Datos no proporcionados o invalidos";
  }
} else {
	$r["continuar"]=true;
	$r["delay"]=date("w",mktime(0,0,1,date("n"),1,date("Y")*1));
	$r["ultimo"]=date("d",mktime(0,0,0,date("n")+1,0,date("Y")*1));
	$r["fecha"]=strtoupper(strftime("%B %Y",mktime(0,0,0,date("n")+1,0,date("Y")*1)));
	$r["sigm"]=date("n",mktime(0,0,0,date("n")+1,1,date("Y")));
	$r["siga"]=date("Y",mktime(0,0,0,date("n")+1,1,date("Y")));
	$r["prevm"]=date("n",mktime(0,0,0,date("n"),0,date("Y")));
	$r["preva"]=date("Y",mktime(0,0,0,date("n"),0,date("Y")));
}
echo json_encode($r);
?>