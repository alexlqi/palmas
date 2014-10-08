// JavaScript Document
$(document).ready(function(e) {
	//para el calendario
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
				console.log(json);
			}
		});
	});
	});
	mActual=$("#mesanio").attr("data-m");
	aActual=$("#mesanio").attr("data-a");
	llenarCalItem(mActual,aActual,function(){
		//callback para usar los elementos recien agregados
		$(".item_calendario").click(function(e) {
			json=$.parseJSON($(this).attr("data-json"));
			if($(this).hasClass("event")){
				console.log(json);
			}
		});
	});
	//termina para el calendario
	
	//para las tabs
    $("#contenido #tabs").tabs({ heightStyle: "content" });
	$(".hover").click(function(e) {
        if(e.target.className=="hover"){
			$(this).fadeOut(300);
		}
    });
	$(".item_agenda").click(function(e) {
        $(".hover").fadeIn(300);
    });
	$(".pdf").click(function(e) {
		datos=$(".apdf").html();
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
	
	//Para la seccion de Inventarios
	listado();
	
	//Para la sección de Entradas al inventario
	$(".cantidad").numeric();
	$(".b_entrada").click(function(e) {
		if($("#entradas_form .id_articulo").find("option:selected").val()!=""){
			datos=$(this).parent().serialize();
			$.ajax({
				url:'scripts/s_alta_inv.php',
				cache:false,
				type:'POST',
				data:datos,
				success: function(r){
					if(r.continuar){
						alerta("info","Existencias modificadas exitosamente");
						//actualizar el listado
						listado();
						resetform();
					}else{
						alerta("error",r.info);
					}
				}
			});
		}else{
			alert("Elige un artículo");
		}
    });
	$(".b_salida").click(function(e) {
		if($("#salidas_form .id_articulo").find("option:selected").val()!=""){
			datos=$(this).parent().serialize();
			$.ajax({
				url:'scripts/s_baja_inv.php',
				cache:false,
				type:'POST',
				data:datos,
				success: function(r){
					if(r.continuar){
						alerta("info","Existencias modificadas exitosamente");
						//actualizar el listado
						listado();
						resetform();
					}else{
						alerta("error",r.info);
					}
				}
			});
		}else{
			alert("Elige un artículo");
		}
    });
	$("#entradas .checar").click(function(){
		s="."+$(this).attr("data-list");
		$("#entradas .listas:not("+s+")").hide();
		$("#entradas "+s).slideToggle(300);
	});
	$("#salidas .checar").click(function(){
		s="."+$(this).attr("data-list");
		$("#salidas .listas:not("+s+")").hide();
		$("#salidas "+s).slideToggle(300);
	});
	$(".reingresar").click(function(e) {
		evento=$(this).attr("data-evento");
		datos={};
		items={};
		campo=$(this).parent().parent().find("input.numerico");
        $.each(campo,function(i,v){
			maximo=$(this).attr("data-max")*1;
			regresaron=$(this).attr("data-regresaron");
			art=$(this).attr("data-art");
			cant=$(this).val();
			(cant>maximo) ? cant=maximo:cant;
			$(this).val(cant);
			cant=cant*1+regresaron*1;
			items[i]={'art':art,'cant':cant};
		});
		
		//levantar un reporte por cada cantidad de articulo no devuelto
		
		datos.id_evento=evento+"";
		datos.items=items;
		mouseLoad(true);
		$.ajax({
			url:'scripts/s_regresar_art.php',
			cache:false,
			type:'POST',
			data:datos,
			success: function(r){
				mouseLoad(false);
				location.reload();
			},
			error: function(r){
				mouseLoad(false);
			}
		});
    });
	$(".autorizar").click(function(e) {
        evento=$(this).attr("data-evento");
		datos={};
		items={};
        $.each($(this).parent().parent().find("input:checked"),function(i,v){
			art=$(this).attr("data-art");
			items[i]={'art':art};
		});
		datos.id_evento=evento+"";
		datos.items=items;
		$.ajax({
			url:'scripts/s_autorizar.php',
			cache:false,
			type:'POST',
			data:datos,
			success: function(r){
				if(r.continuar){
					alerta("info","Artículos autorizados");
					//actualizar el listado
					listado();
				}else{
					alerta("error",r.info);
				}
			}
		});
    });
	//para la seccion de salidas
	$(".apdf").click(function(e) {
        datos=$.parseJSON($(this).attr("data-json"));
		nombre="pruebajsonpdf";
		$.ajax({
			url:'scripts/almacen_salida_pdf.php',
			cache:false,
			type:'POST',
			data:{
				datos:datos,
				nombre:nombre,
				orientar:'portrait'
			},
			success: function(r){
				window.open(r,nombre,'_blank');
			}
		});
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

//llenar la lista de almacén
//actualizar el listado
function listado(){
	$.ajax({
		url:'scripts/s_check_inv.php',
		cache:false,
		success: function(r){
			$("#inventario").html(r);
		}
	});
}