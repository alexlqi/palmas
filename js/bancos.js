// JavaScript Document
$(document).ready(function(e) {
    //alerta("info","Vista de Bancos NO DISPONIBLE todavía");
});
function edocuenta(e){
	$(".edocuenta").hide();
	s=$(e).attr("data-id");
	$(s).show();
	console.log(s);
}