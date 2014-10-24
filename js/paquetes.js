// JavaScript Document
$(document).ready(function(e) {
    $(".unidades").autocomplete({
		source: "scripts/busca_unidades.php",
		minLength: 2,
		select: function(event, ui) {
			//asignacion individual alos campos
			$("this").val(ui.item.nombre);
		}
	});
	$("input.eliminar").click(function(e) {
		id=$(".id_paquete").val();
		if(id!=""){
		  if(confirm("Seguro que quiere eliminar este artículo?")){
			$.ajax({
				url:'scripts/s_eliminar_paquetes.php',
				cache:false,
				type:'POST',
				data:{
					id:id
				},
				error: function(r){
					alerta("error",r.statusText);
				},
				success: function(r){
					if(r.continuar){
						alerta("info","Paquete eliminado exitosamente");
						buscarClave();
					}else{
						alerta("error",r.info);
					}
				}
			});
		  }
		}else{
			alerta("error","Elige un paquete valido primero");
			$(".clave").animate({
				"background-color":"rgba(255,0,0,0.3)",
			},1000);
			$(".clave").animate({
				"background-color":"none",
			},1000);
		}
    });
});