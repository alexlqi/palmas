// JavaScript Document
$(document).ready(function(e) {
    //alerta("info","Vista de eventos NO DISPONIBLE todavía");
	$("#tabs").tabs({
		heightstyle:"content"
	});
	$( ".clave_evento" ).keyup(function(){
		_this=$(this);
		if(typeof timer=="undefined"){
			timer=setTimeout(function(){
				buscarClaveGet();
			},300);
		}else{
			clearTimeout(timer);
			timer=setTimeout(function(){
				buscarClaveGet();
			},300);
		}
    }); //termina buscador de evento
	
	//para añadir más articulos al evento
	$(".agregar_articulo").click(function(){
		id_evento=$(".id_evento").get(0).value;
		id=$(".lista_articulos").length+1;
		$("#articulos").append('<tr id="'+id+'" class="lista_articulos"><td style="background-color:#FFF;"><input type="hidden" class="id_item" value="" /><input type="hidden" class="id_evento" value="" /><input type="hidden" class="id_articulo" /><input type="hidden" class="id_paquete" /></td><td><input class="cantidad" type="text" size="7" onkeyup="cambiar_cant('+id+')" /></td><td><input class="articulo_nombre text_full_width" onkeyup="art_autocompletar('+id+');" /></td><td>$<input type="text" class="precio" /></td><td>$<span class="total"></span></td><td><span class="guardar_articulo" onclick="guardar_art('+id+')"></span><span class="eliminar_articulo" onclick="eliminar_art('+id+')"></span></td></tr>');
		$.each($(".lista_articulos"),function(i,v){
			$(this).find(".id_evento").val(id_evento);
		});
		$(".cantidad").numeric();
	});
	
	//para ver el formulario de pago
	$(".agregarpago").click(function(e) {
        $("#nuevopago").slideToggle(200);
    });
	//para ver historial de pago
	$(".historial").click(function(e) {
        $("#historial").slideToggle(200);
    });
	//para añadir pago
	$(".anadir").click(function(e) {
		eve=$(".id_evento").get(0).value;
		monto=$(".importe").val();
		fecha=$(".fechapago").val();
		cliente=$(".id_cliente").val();
		$.ajax({
			url:'scripts/s_pagar.php',
			cache:false,
			type:'POST',
			data:{
				'eve':eve,
				'monto':monto,
				'fecha':fecha,
				'cliente':cliente
			},
			success: function(r){
				if(r.continuar){
					alerta("info","Pago añadido exitosamente");
					checarTotalEve(eve);
					historial(evento);
					$("#nuevopago input[type=text]").val('');
				}else{
					alerta("error",r.info);
				}
			}
		});
	});
});
function historial(eve){
	$.ajax({
		url:'scripts/s_historial_pago.php',
		cache:false,
		type:'POST',
		data:{
			'eve':eve
		},
		success: function(r){
			$("#historial .mostrar").html(r);
		}
	});
	//funcion para ver el historial de pagos del evento
}
function buscarClaveGet(){
	evento="";
	dato=$(".clave_evento").val();
	input=$(".clave_evento");
	input.addClass("ui-autocomplete-loading-left");
	$.ajax({
	  url:"scripts/busca_evento.php",
	  cache:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		form="eventos";
		//console.log(r);
		//asigna el valor en el campo
		
		//añade selecciona option del select
		if(r.bool){
			//graba los datos en los campos correspondientes
			value=r.id_tipo;
			$(".id_tipo option[value='"+value+"']").prop("selected",true);
			
			//para los radio
			eventosalon=r.eventosalon;
			$(".eventosalon").parent().find("."+eventosalon+"r").click();
			
			$.each(r,function(i,v){
				if(i!="label" && i!="id_tipo" && i!="tipo"){
					//console.log(i+" "+v);
					selector="#"+form+" ."+i
					$(selector).val(v);
				}
			});//*/
			
			//asigna el id de cotización
			evento=r.id_evento;
			$(".id_evento").val(evento);
			get_items_eve(evento);
			checarTotalEve(evento);
			historial(evento);
			//le da el nombre al boton
			$(".guardar").hide();
			$(".modificar").show();
		}else{
			$.each($("#hacer form"),function(i,v){
				$(this).get(i).reset();
			});
			alerta("info","Este evento no se ha generado o no existe");
		}
		input.removeClass("ui-autocomplete-loading-left");
	  }
	});
}
function get_items_eve(id){
	$(".lista_articulos").remove();
	$.ajax({
		url:'scripts/get_items_eve.php',
		cache:false,
		async:false,
		data:{
			'id_evento':id
		},
		success: function(r){
			$("#articulos").append(r);
		}
	});
}
function checarTotalEve(id){
	var total;
	$.ajax({
		url:'scripts/s_check_total_eve.php',
		cache:false,
		async:false,
		type:'POST',
		data:{
			'id':id
		},
		success: function(r){
			if(r.continuar){
				$(".totalevento").val(r.total);
				$(".restante").val(r.restante);
			}else{
				alerta("error",r.info);
			}
		}
	});
}
function editar(e){
	s=$(e);
	$(".clave").val(s.attr("data-cve"));
	buscarClaveGet();
	$(".hacer a")[0].click();
}
function quitar_verde(e){
	$(e).parent().parent().removeClass("verde_ok");
}

function guardar_art(elemento){
	row=$("#"+elemento);
	padre=$("#"+elemento).parent();
	
	//mostrar que se esta procesando
	procesando("mostrar",0);
	
	//checa si se modificó el total
	actTotal=true;
	if(row.hasClass("verde_ok")){
		actTotal=false;
	}
	
	id_item=$("#"+elemento+" .id_item").val();
	id_articulo=$("#"+elemento+" .id_articulo").val();
	id_paquete=$("#"+elemento+" .id_paquete").val();
	id_evento=$(".id_evento").first().val();
	cantidad=$("#"+elemento+" .cantidad").val();
	precio=$("#"+elemento+" .precio").val();
	total=$("#"+elemento+" .total").html();
	$.ajax({
		url:'scripts/guarda_art_eve.php',
		cache:false,
		type:'POST',
		data:{
			'id_item':id_item,
			'id_paquete':id_paquete,
			'id_articulo':id_articulo,
			'id_evento':id_evento,
			'cantidad':cantidad,
			'precio':precio,
			'total':total,
			boolTotal:actTotal
		},
		success: function(r){
			procesando("ocultar",0); //0 lo dispara automaticamente
			if(r.continuar){
				$("#"+elemento+" .id_item").val(r.id_item);
				padre.find(".id_evento").val(id_evento);
				alerta("info",r.info);
				row.addClass("verde_ok");
				setTimeout(function(){checarTotal('eventos',id_evento);},500);
			  }else{
				alerta("error",r.info);
			  }
		}
	});
}
function eliminar_art(elemento){
	id_evento=$(".id_evento").first().val();
	id_item=$("#"+elemento+" .id_item").val();
	if(id_item!=0){
		$.ajax({
			url:'scripts/quita_art_eve.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r.continuar){
				alerta("info",r.info);
				$("#"+elemento).remove();
				checarTotal('eventos',id_evento);
			  }else{
				alerta("error",r.info);
			  }
			}
		});
	}else{
		$("#"+elemento).remove();
	}
}
function art_autocompletar(id){
	padre=$("#"+id);
	cantidad=padre.find(".cantidad").val()*1;
	id_articulo=padre.find(".id_articulo");
	id_paquete=padre.find(".id_paquete");
	precio=padre.find(".precio").parent();
	total=padre.find(".total");
	$( "#"+id+" .articulo_nombre").autocomplete({
	  source: "scripts/busca_articulos.php",
	  minLength: 1,
	  select: function( event, ui ) {
		  total.parent().parent().removeClass("verde_ok");
		  id_articulo.val(ui.item.id_articulo);
		  id_paquete.val(ui.item.id_paquete);
		  precio.html(ui.item.precio);
		  totalca=cantidad*ui.item.precio;
		  total.html(totalca);
	  }
	});
}
function cambiar_cant(id){
	padre=$("#"+id);
	cantidad=padre.find(".cantidad").val()*1;
	precio=padre.find(".precio").val()*1;
	total=cantidad*precio;
	padre.find(".total").html(total);
	padre.removeClass("verde_ok");
}
function darprecio(e){
	precio=$(e).val();
	$(e).parent().parent().removeClass("verde_ok");
	cant=$(e).parent().parent().find(".cantidad").val();
	$(e).siblings(".precio").val(precio);
	total=(precio*1)*(cant*1);
	$(e).parent().parent().find(".total").html(total);
}
function autorizarEve(id,clave){
	$.ajax({
		url:'scripts/s_autorizar_evento.php',
		cache:false,
		type:'POST',
		data:{
			id_evento:id
		},
		success: function(r){
			if(r.continuar){
				alerta('info','El evento '+clave+' ha sido autorizado');
				$("tr.cot"+clave).find(".bestatus").html('Evento');
			}else{
				alerta('error',r.info);
			}
		}
	});
}
function revocarEve(id,clave){
	$.ajax({
		url:'scripts/s_revocar_evento.php',
		cache:false,
		type:'POST',
		data:{
			id_evento:id
		},
		success: function(r){
			if(r.continuar){
				alerta('info','El evento '+clave+' ha sido revocado');
				$("tr.cot"+clave).find(".bestatus").html('Sin autorizar');
			}else{
				alerta('error',r.info);
			}
		}
	});
}
function checarTotal(tabla,id){
	var total;
	$.ajax({
		url:'scripts/s_check_total_eve.php',
		cache:false,
		async:false,
		type:'POST',
		data:{
			'tabla':tabla,
			'id':id
		},
		success: function(r){
			if(r.continuar){
				$(".totalevento").val(r.total);
			}else{
				alerta("error",r.info);
			}
		}
	});
}