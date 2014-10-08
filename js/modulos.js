// JavaScript de MODULOS
$(document).ready(function(e) {
	$("#tabs").tabs({ heightStyle: "content" });
	rapidez=500;
	 $(".boton_abrir_form_dos").click(function(e) {
	  if(ingresar){
		ingresar=false;
		loading=$(this).find(".loading")
		loading.show();
		var checar=false;
        form=$(this).attr("data-form");
		metodo=$(this).attr("data-m");
		$.ajax({
			url:"partes/"+form,
			cache:false,
			error: function(r){
				loading.hide();
				alerta("error",r.status+" "+r.statusText);
				ingresar=true;
			},
			success: function(r){
				$("#formularios_modulo").css("display","none").html(r);
				loading.hide();
				checar=true;
			}
		});
		
		//interval para mostar la info puesta por el ajax
		putter=setInterval(function(){
			if(checar){
				$("#botones_modulo").fadeOut(rapidez,function(){
					$("#formularios_modulo").show("slide",{direction:'right'},rapidez);
				});
				//para desactivar el interval
				clearInterval(putter);
				checar=false;
			}
		},100);
	  }
    });
});

//función para continuar si los campos requeridos
function requerido(seccion){
	selector=seccion + " .requerido";
	continuar=true;
	$.each($(selector),function(i,v){
		console.log(v);
		if($(this).val()==""){
			$(this).addClass("falta_llenar");
			continuar=false;
		}
	});
	return continuar;
}

function filtro1(e){
	f=$(e).find("option:selected").val();
	$.ajax({
		url:'',
		cache:false,
		type:'POST',
		data:{
			'f':f
		},
		success: function(r){
			$(".filtro1").html('<option value="todos">Todos</option>');
			$(".filtro1").append(r.opt);
		}
	});
}