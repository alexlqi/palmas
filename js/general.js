// JavaScript Cosas generales
//variables globales
var ingresar=true;
$(document).ready(function(e) {
	//constante de URL en la que se está
	URL=document.URL;
	
	if ($("html").hasClass("ie6") || $("html").hasClass("ie7")){
		$(".celda").wrap("<td />");
		$(".fila").wrap("<tr />");
		$(".tabla").wrapInner("<table />");
	}//*/
	$(".link").click(function(e) {
        window.location=$(this).attr("data-url");
    });
	$("#alerta_error, #alerta_info").click(function(e) {
        $(this).fadeOut(200);
    });
	
	//para el calendario
	$("#semana1 .celda:odd, #semana3 .celda:odd, #semana5 .celda:odd").addClass("resalta_gris");
	$("#semana2 .celda:even, #semana4 .celda:even, #semana6 .celda:even").addClass("resalta_gris");
	$(".semana .celda").mouseenter(function(e) {
        $(this).addClass("resalta_azul");
    });
	$(".semana .celda").mouseleave(function(e) {
        $(this).removeClass("resalta_azul");
    });
	//termina para el calendario
	
	//para las tablas
	$("tr:odd td").addClass("fondo_gris");
	//termina para las tablas
	
	//hacer numerico para elementos con la clase numerico
	$(".numerico").numeric();
	
});
//funciones del calendario
function llenarCalNum(mes,anio){
	$(".contenido_dia").attr("id","");
	flag_s6=true;
	//$("div.contenido_dia").attr('id',"");
	$("#semana6").hide();
	if(mes===null && anio===null){
	  datos=null;
	}else{
	  datos={m:mes,a:anio};
	}
	$.ajax({
		url:'scripts/s_dias_calendario.php',
		cache:false,
		async:false,
		type:'POST',
		data:datos,
		success: function(r){
		  if(r.continuar){
			//script para escribir el mes y el año de la busqueda
			$("#mesanio").html(r.fecha);
			$("#mes_previo").attr("data-m",r.prevm);
			$("#mes_previo").attr("data-a",r.preva);
			$("#mes_siguiente").attr("data-m",r.sigm);
			$("#mes_siguiente").attr("data-a",r.siga);
			//script para llebar el numero del día en el calendario
			dia=1;
			delay=r.delay;
			maximo=r.ultimo;
			$(".semana .dia_der_top").each(function(index, element) {
				
				//condición para mostrar semana 6 si hay días en ella
				if($(this).parent().parent().parent().attr("id")=="semana6" && dia<maximo && flag_s6){
					$("#semana6").show();
					flag_s6=false;
				}
				if(delay>0){
				  delay--;
				} else {
				  idDia=dia;
				  if(dia<10){ dia="0"+dia;}
				  if(dia>maximo){
					$(this).html("");
					$(this).parent().attr('id',"");
				  }else{
					//escribe el numero de día y lo asigna a la celda
					$(this).parent().attr('id',"dia"+idDia);
					$(this).parent().attr("data-dia",dia)
					$(this).html(dia);
				  }
				  dia++;
				}
			});
		  }else{
			alert(r.info);
		  }
		}
	});
}
//termina función del calendario

function alerta(tipo,info,handler){
	if(tipo=="error"){
		$("#alerta_error p").html(info);
		$("#alerta_error").fadeIn(100);
		$("#alerta_error, #alerta_info").click(function(e) {
			$(this).fadeOut(200,function(){
				if(typeof handler!="undefined"){handler();}
			});
		});
	}else{
		$("#alerta_info p").html(info);
		$("#alerta_info").fadeIn(100);
		$("#alerta_info, #alerta_info").click(function(e) {
			$(this).fadeOut(200,function(){
				if(typeof handler!="undefined"){handler();}
			});
		});
	}
}

//función para llenar el calendario con fechas
function llenarCalNum(mes,anio){
	$(".contenido_dia").attr("id","");
	flag_s6=true;
	//$("div.contenido_dia").attr('id',"");
	$("#semana6").hide();
	if(mes===null && anio===null){
	  datos=null;
	}else{
	  datos={m:mes,a:anio};
	}
	$.ajax({
		url:'scripts/s_dias_calendario.php',
		cache:false,
		async:false,
		type:'POST',
		data:datos,
		success: function(r){
		  if(r.continuar){
			//script para escribir el mes y el año de la busqueda
			$("#mesanio").html(r.fecha);
			$("#mes_previo").attr("data-m",r.prevm);
			$("#mes_previo").attr("data-a",r.preva);
			$("#mes_siguiente").attr("data-m",r.sigm);
			$("#mes_siguiente").attr("data-a",r.siga);
			//script para llebar el numero del día en el calendario
			dia=1;
			delay=r.delay;
			maximo=r.ultimo;
			$(".semana .dia_der_top").each(function(index, element) {
				
				//condición para mostrar semana 6 si hay días en ella
				if($(this).parent().parent().parent().attr("id")=="semana6" && dia<maximo && flag_s6){
					$("#semana6").show();
					flag_s6=false;
				}
				if(delay>0){
				  delay--;
				} else {
				  idDia=dia;
				  if(dia<10){ dia="0"+dia;}
				  if(dia>maximo){
					$(this).html("");
					$(this).parent().attr('id',"");
				  }else{
					//escribe el numero de día y lo asigna a la celda
					$(this).parent().attr('id',"dia"+idDia);
					$(this).parent().attr("data-dia",dia)
					$(this).html(dia);
				  }
				  dia++;
				}
			});
		  }else{
			alert(r.info);
		  }
		}
	});
}

//resetar todos los forms
function resetform(){
	$.each($("body").find("form"),function(i,v){
		this.reset();
	});
}
function mouseLoad(ctrl){
	if(ctrl){
		$("body").addClass("mouseload");
	}else{
		$("body").removeClass("mouseload");
	}
}

//scrollear hasta arriba
function scrollTop(){
	$('html, body').animate({ scrollTop: 0 }, 'slow');
}

//para eliminar caracteres no queridos
function mitrim(x,look) {
    return x.replace(look,'');
}