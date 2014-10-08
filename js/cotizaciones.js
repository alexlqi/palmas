// JavaScript cotizaciones
$(document).ready(function(e) {
	//genera las tabs
	aguarda=false;
	$("form").keydown(function(){
		aguarda=true;
	});
	$(".guardar").click(function(e) {
		if(requerido()){
        	setTimeout(function(){buscarClaveGet();},500);
		}
    });
	$("#contenido #tabs").tabs({ heightStyle: "content" });
	
	//para mostrar y ocultar el listado de salones
	$(".salonr").click(function(e) {
		$(".eventosalon_h").val('salon');
        $(".salones *").show();
    });
	$(".eventor").click(function(e) {
		$(".eventosalon_h").val('evento');
        $(".salones *").hide();
    });
	
	//busca cliente
	$( ".cliente_cotizacion" ).autocomplete({
      source: "scripts/busca_clientes.php",
      minLength: 1,
      select: function( event, ui ) {
		//da el nombre del formulario para buscarlo en el DOM
		form="cotizaciones";
		
		//asignacion individual alos campos
		$("#cotizaciones .id_cliente").val(ui.item.id_cliente);
	  }
    });
	
	//script para añadir articulos en la cotización
	id_cotizacion=$(".id_cotizacion").first().val();
	$(".agregar_articulo").click(function(){
		id=$(".lista_articulos").length+1;
		$("#articulos").append('<tr id="'+id+'" class="lista_articulos"><td style="background-color:#FFF;"><input type="hidden" class="id_item" value="" /><input type="hidden" class="id_cotizacion" value="" /><input type="hidden" class="id_articulo" /><input type="hidden" class="id_paquete" /></td><td><input class="cantidad" type="text" size="7" onkeyup="cambiar_cant('+id+')" /></td><td><input class="articulo_nombre text_full_width" onkeyup="art_autocompletar('+id+');" /></td><td>$<span class="precio"></span></td><td>$<span class="total"></span></td><td><span class="guardar_articulo" onclick="guardar_art('+id+')"></span><span class="eliminar_articulo" onclick="eliminar_art('+id+')"></span></td></tr>');
		$.each($(".lista_articulos"),function(i,v){
			$(this).find(".id_cotizacion").val(id_cotizacion);
		});
		$(".cantidad").numeric();
	});
	
	//script para buscr cotización por clave
	var timer;
	$( ".clave_cotizacion" ).keyup(function(){
		_this=$(this);
		if(typeof timer=="undefined"){
			timer=setTimeout(function(){
				buscarClaveGet()
			},300);
		}else{
			clearTimeout(timer);
			timer=setTimeout(function(){
				buscarClaveGet();
			},300);
		}
    }); //termina buscador de cotizacion
	
	//sección para mis cotizaciones
		//filtros
	$(".filtro").keyup(function(e) {
		if(e.keyCode!=9){
			if($(this).val()!=""){
				buscar=$(this).val();
				criterio=$(this).attr("data-c");
				$("."+criterio+":not(:contains("+buscar+")):visible").parent().hide();
				$("."+criterio+":contains("+buscar+"):visible").parent().show();
			}else{
				$(".listado *").show();
			}
		}
    });
	
	$(".eliminar").click(function(e) {
		padre=$(this).parent().parent();
        if(confirm("¿Seguro que desea cancelar esta cotización?")){
			cve=$(this).attr("data-cve");
			$.ajax({
				url:'scripts/s_cancelar_cot.php',
				cache:false,
				type:'POST',
				data:{
					cve:cve
				},
				success: function(r){
					if(r.continuar){
						alerta("info","Cotización cancelada");
						padre.find(".bestatus").html('0');
					}else{
						alerta("error",r.info);
					}
				}
			});
		}
    });
	
	$(".filtro").change(function(e) {
		if($(this).val()!=""){
			buscar=$(this).val();
			criterio=$(this).attr("data-c");
			$("."+criterio+":not(:contains("+buscar+"))").parent().hide();
			$("."+criterio+":contains("+buscar+")").parent().show();
		}else{
			$(".listado *").show();
		}
    });
	//termina mis cotizaciones
	//generar pdf
	$(".pdf").click(function(e) {
		datos=$("#mias").html();
		nombre=$(this).attr("data-nombre");
		orientar=$(this).attr("data-orientar");
        $.ajax({
			url:'scripts/getpdf.php',
			cache:false,
			type:'POST',
			data:{
				'nombre':nombre,
				'html':datos,
				'orientar':orientar
			},
			success: function(r){
				window.open(r,'Reporte del evento','toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=800, height=600');
			}
		});
    });
	$(".metodo").change(function(e) {
		$(".divplazos").hide();
		$(".divbancos").hide();
        if($(this).find("option:selected").val()=="credito"){
			$(".divplazos").show();
		}else if($(this).find("option:selected").val()=="transferencia"){
			$(".divbancos").show();
		}
    });
	$(".anticipo").keyup(function(e) {
		anticipo=$(this).val();
		total=$(".totalevento").val();
        $(".restante").val(total-anticipo);
    });
});

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
	
	id_cotizacion=$(".id_cotizacion").first().val();
	
	if(id_cotizacion!=""){
		id_item=$("#"+elemento+" .id_item").val();
		id_articulo=$("#"+elemento+" .id_articulo").val();
		id_paquete=$("#"+elemento+" .id_paquete").val();
		cantidad=$("#"+elemento+" .cantidad").val();
		precio=$("#"+elemento+" .precio").html();
		total=$("#"+elemento+" .total").html();
		$.ajax({
			url:'scripts/guarda_articulo_cot.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item,
				'id_paquete':id_paquete,
				'id_articulo':id_articulo,
				'id_cotizacion':id_cotizacion,
				'cantidad':cantidad,
				'precio':precio,
				'total':total
			},
			success: function(r){
				if(r.continuar){
					$("#"+elemento+" .id_item").val(r.id_item);
					padre.find(".id_cotizacion").val(id_cotizacion);
					alerta("info",r.info);
					row.addClass("verde_ok");
					setTimeout(function(){checarTotal('cotizaciones',id_cotizacion);},500);
				  }else{
					alerta("error",r.info);
				  }
			}
		});
	}else{
		alert("Debes guardar la cotización primero");
	}
}
function eliminar_art(elemento){
	id_cotizacion=$(".id_cotizacion").first().val();
	id_item=$("#"+elemento+" .id_item").val();
	if(id_item!=0){
		$.ajax({
			url:'scripts/elimina_articulo.php',
			cache:false,
			type:'POST',
			data:{
				'id_item':id_item
			},
			success: function(r){
			  if(r.continuar){
				alerta("info",r.info);
				$("#"+elemento).remove();
				checarTotal('cotizaciones',id_cotizacion);
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
	precio=padre.find(".precio").html()*1;
	total=cantidad*precio;
	padre.find(".total").html(total);
	padre.removeClass("verde_ok");
}
function get_items_cot(id){
	$(".lista_articulos").remove();
	$.ajax({
		url:'scripts/get_items_cot.php',
		cache:false,
		async:false,
		data:{
			'id_cotizacion':id
		},
		success: function(r){
			$("#articulos").append(r);
		}
	});
}

function buscarClaveGet(){
	cotizacion="";
	$(".totalevento").val('');
	$(".restante").val('');
	$(".eventosalon").prop("checked",false);
	dato=$(".clave_cotizacion").val();
	input=$(".clave_cotizacion");
	input.addClass("ui-autocomplete-loading-left");
	$.ajax({
	  url:"scripts/busca_cotizacion.php",
	  cache:false,
	  data:{
		term:dato
	  },
	  success: function(r){
		form="cotizaciones";
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
			//realiza las otras tareas
			//asigna el id de cotización
			cotizacion=r.id_cotizacion;
			get_items_cot(cotizacion);
			checarTotal('cotizaciones',cotizacion);
			
			//muestra el boton de modificar y oculta el de guardar
			$(".guardar").hide();
			$(".modificar").show();
		}else{
			$.each($("#hacer form"),function(i,v){
				$(this).get(i).reset();
			});
			alerta("info","Cotización no existe o ya es un evento");
			//le da el nombre al boton
			$(".guardar").show();
			$(".modificar").hide();
		}
		input.removeClass("ui-autocomplete-loading-left");
	  }
	});
}
function darprecio(e){
	precio=$(e).val();
	$(e).parent().parent().removeClass("verde_ok");
	cant=$(e).parent().parent().find(".cantidad").val();
	$(e).siblings(".precio").html(precio);
	total=(precio*1)*(cant*1);
	$(e).parent().parent().find(".total").html(total);
}

function pasarevento(){
	//clave
	clave=$(".clave_cotizacion").val();
	//aqui la sección para poner los datos de la cuenta
	continuar=false;
	if($("#cuenta .anticipo").val()>0){
		continuar=true;
	}else{
		alert("Necesita registrar un anticipo");
	}
	if(continuar){
		//pasar a evento
		id=$("body").find(".id_cotizacion").get(0).value;
		total=$(".totalevento").val();
		anticipo=$(".anticipo").val();
		metodo=$(".metodo").val();
		plazos=$(".plazos").val();
		$.ajax({
			url:'scripts/s_pasaraevento.php',
			cache:false,
			async:true,
			type:'POST',
			data:{
				id_cotizacion:id,
				'total':total,
				'anticipo':anticipo
			},
			success: function(r){
				if(r.continuar){
					alerta("info","Se ha generado el evento exitosamente!");
					 window.location="eventos.php?cve="+clave;
				}else{
					alerta("error",r.info);
				}
			}
		});
	}
}

function checarTotal(tabla,id){
	var total;
	$.ajax({
		url:'scripts/s_check_total.php',
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