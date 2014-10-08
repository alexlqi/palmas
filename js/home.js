// JavaScript Para la sección home
$(document).ready(function(e) {
	llenarCalNum();
	$(".mover_calendario").click(function(e) {
		$(".item_calendario").unbind("click");
		$(".semana .dia_der_top").html('');
		$(".contenido_dia p").remove();
		m=$(this).attr('data-m');
		a=$(this).attr('data-a');
		$("#mesanio").attr("data-m",m);
		$("#mesanio").attr("data-a",a);
        llenarCalNum(m,a);
		llenarCalItem(m,a,function(){
			//callback para usar los elementos recien agregados
			$(".item_calendario").click(function(e) {
				json=$.parseJSON($(this).attr("data-json"));
				if($(this).hasClass("event")){
					//llevarlo directamente al evento
					window.location="http://"+window.location.hostname+"/eventos.php?cve="+json.clave;
					
					/*$(".info_titulo span").html(json.clave);
					$(".tipo_evento").html(json.tipo_evento);
					$(".nombre_evento").html(json.nombre);
					$(".nombre_cliente").html(json.nombre_cliente);
					$(".fechaevento").html(json.fechaevento);
					$(".info_boton").val('ir al evento').attr("data-sig","evento").attr("data-id",json.clave);
					$("#display_info").fadeIn(100);*/
				}else{
					//llevarlo directamente al evento
					window.location="http://"+window.location.hostname+"/cotizaciones.php?cve="+json.clave;
					
					/*$(".info_titulo span").html(json.clave);
					$(".tipo_evento").html(json.tipo_evento);
					$(".nombre_evento").html(json.nombre);
					$(".nombre_cliente").html(json.nombre_cliente);
					$(".fechaevento").html(json.fechaevento);
					$(".info_boton").val('ir a la cotización').attr("data-sig","cotizacion").attr("data-id",json.clave);
					$("#display_info").fadeIn(100);*/
				}
			});
		});
    });
	
	$('#display_info').click(function(e) {
		if (e.target.className === "celda"){
			$('#display_info').fadeOut(200);
		}
	});
	
	mActual=$("#mesanio").attr("data-m");
	aActual=$("#mesanio").attr("data-a");
	
	//lena el mes con los eventos y cotizaciones
	llenarCalItem(mActual,aActual,function(){
		//callback para usar los elementos recien agregados
		$(".item_calendario").click(function(e) {
			json=$.parseJSON($(this).attr("data-json"));
			if($(this).hasClass("event")){
				//llevarlo directamente al evento
				window.location="http://"+window.location.hostname+"/eventos.php?cve="+json.clave;
				
				/*$(".info_titulo span").html(json.clave);
				$(".tipo_evento").html(json.tipo_evento);
				$(".nombre_evento").html(json.nombre);
				$(".nombre_cliente").html(json.nombre_cliente);
				$(".fechaevento").html(json.fechaevento);
				$(".info_boton").val('ir al evento').attr("data-sig","evento").attr("data-id",json.clave);
				$("#display_info").fadeIn(100);*/
			}else{
				//llevarlo directamente al evento
				window.location="http://"+window.location.hostname+"/cotizaciones.php?cve="+json.clave;
				
				/*$(".info_titulo span").html(json.clave);
				$(".tipo_evento").html(json.tipo_evento);
				$(".nombre_evento").html(json.nombre);
				$(".nombre_cliente").html(json.nombre_cliente);
				$(".fechaevento").html(json.fechaevento);
				$(".info_boton").val('ir a la cotización').attr("data-sig","cotizacion").attr("data-id",json.clave);
				$("#display_info").fadeIn(100);*/
			}
		});
	});
	
	//ir a cotización o evento
	$(".info_boton").click(function(e) {
		sig=$(this).attr("data-sig");
		id=$(this).attr("data-id");
		//aquí hay que poner un switch cuando se hagan los modulos restantes
		if(sig=="cotizacion"){
			window.location="http://"+document.domain+"/cotizaciones.php?cve="+id;
		}else{ //va a evento
			window.location="http://"+document.domain+"/eventos.php?cve="+id;
		}
    });
	
	//ocultar por estatus
	$("#estatus span").click(function(e) {
		clase="."+$(this).attr('data-sel');
		$(".item_calendario").hide();
		$(clase).show();
		if($(this).hasClass("desvanecer")){
			$.each($("#estatus span"),function(){
				if(!$(this).hasClass("desvanecer")){
					$(this).addClass("desvanecer");
				}
			});
			$(this).removeClass("desvanecer");
			//le pone a todos el desvanecer
		}else{
			//le quita al clickado el desvanecer
			$(this).addClass("desvanecer");
			$(".item_calendario").show();
		}
    });
	
	//filtro de tipo de proyect
	$("#tipo_proyecto").change(function(e) {
		opcion=$(this).val();
		switch(opcion){
			case 'ambos':
				$(".event").show();
				$(".cotizacion").show();
			break;
			case 'cotizacion':
				$(".event").hide();
				$(".cotizacion").show();
			break;
			case 'event':
				$(".event").show();
				$(".cotizacion").hide();
			break;
			default:
				$(".event").show();
				$(".cotizacion").show();
			break;
		}
    });
	$("#tipo_evento").change(function(e) {
		opcion=$(this).val();
		selector=".tipoe"+opcion;
		$(".item_calendario").hide();
		if(opcion=="todos"){
			$(".item_calendario").show();
		}else{
			$(selector).show();
		}
    });
});

//función para llenar el calendario con items 
function llenarCalItem(mes,anio,handler){
	$.ajax({
		url:'scripts/s_items_calendario.php',
		async:false,
		cache:false,
		type:'POST',
		data:{
			m:mes,
			a:anio
		},
		success: function(r){
		  if(r.continuar){
			//each para cada cotización
			if(r.data.c!=null){
		      $.each(r.data.c,function(i,v){
				  console.log(i);
				  $.each(v,function(ii,vv){
					$(i).append('<p id="cot'+vv.id_cotizacion+'" class="item_calendario cotizacion '+vv.estatus+' tipoe'+vv.id_tipo+'" data-json='+"'"+JSON.stringify(vv)+"'"+'>'+vv.nombre+'</p>');
				  });
		      });
			}else{
			  console.log(r.info);
		    }
			if(r.data.e!=null){
		      $.each(r.data.e,function(i,v){
				$.each(v,function(ii,vv){
				  $(i).append('<p id="cot'+vv.id_evento+'" class="item_calendario event '+vv.estatus+'" data-json='+"'"+JSON.stringify(vv)+"'"+'>'+vv.nombre+'</p>');
				});
			  });
			}
		  }else{
			  console.log(r.info);
		  }
		}
	});
	if(typeof handler !='undefined'){handler();}
}