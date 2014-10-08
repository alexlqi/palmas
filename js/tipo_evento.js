// JavaScript Document
$(document).ready(function(e) {
    //busca cliente
	$( ".nombre" ).autocomplete({
      source: "scripts/busca_tipo_evento.php",
      minLength: 1,
      select: function( event, ui ) {
		//asignacion individual alos campos
		$("#f_tipo_evento .id_tipo").val(ui.item.id_tipo);
		$(".modificar").show();
		$(".guardar_individual").hide();
	  }
    });
	$(".nombre").keyup(function(e) {
        if(e.keyCode==8){
			if($(this).val()==""){
				$(".modificar").hide();
				$(".guardar_individual").show();
			}
		}
    });
});