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
});