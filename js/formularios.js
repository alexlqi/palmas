// JavaScript de FORMULARIOS
$(document).ready(function(e) {
	//quita el fondo rojo de falto llenar
	$(".requerido").on("keyup",function(e){
		if($(this).val()!=""){
			$(this).removeClass("falta_llenar");
		}
	});
	
	$(".nueva").click(function(e) {
		
        $.each($("form"),function(i,v){
			this.reset();
		});
		//limpia los campos input ocultos
		$("input[type=hidden]").val('');
		
		//parte donde se eliminan las filas especificas
		$(".lista_articulos").remove();
		$.ajax({
			url:'scripts/'+$(this).attr("data-s")+'.php',
			cache:false,
			async:false,
			success: function(r){
				$(".clave").val(r);
			}
		});
		$(".guardar").show();
		$(".modificar").hide();
    });
	
	//escribe en mayuscula la letra del cmapo con la clase mayuscula
	$('.mayuscula').keyup(function(){
		this.value = this.value.toUpperCase();
	});
	
	//pone el calendario en donde sea el campo fecha 
	$(".fecha").datetimepicker({ 
		changeMonth: true,
		changeYear: true,
		dateFormat: "dd/mm/yy",
		onSelect:function(text,inst){
			var weekday=new Array(7);
			weekday[0]="Domingo";
			weekday[1]="Lunes";
			weekday[2]="Martes";
			weekday[3]="Miércoles";
			weekday[4]="Jueves";
			weekday[5]="Viernes";
			weekday[6]="Sábado";
			
			date=$(this).datepicker('getDate');
			
			$(this).parent().parent().find("abbr").attr("title",weekday[date.getUTCDay()]);
			$(this).parent().parent().find(".borrar_fecha").show();
			$(this).removeClass("falta_llenar");
		}
	});
	$(".fechasql").datetimepicker({ 
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd"
	});
	
	$(".filtrofecha").datepicker().unbind('focus').dblclick(function(e) {
		$.datepicker.setDefaults({ 
			changeMonth: true,
			changeYear: true,
			dateFormat: "dd/mm/yy"
		});
		$(this).datepicker('show');
	});
	
	$(".borrar_fecha").click(function(e) {
		$(this).hide();
        selector="."+$(this).attr("data-class");
		$(selector).val('');
		$(selector).parent().parent().find("abbr").attr("title","");
    });
	
	//boton guardar unica tabla
	$(".boton_dentro").click(function(e) {
        $(".guardar").show();
		$(".modificar").hide();
		$.each($("#formularios_modulo form"),function(i,v){
			$("#formularios_modulo form").get(i).reset();
		});
    });
	
	$( ".nombre_buscar" ).autocomplete({
      source: "scripts/busca_clientes.php",
      minLength: 2,
      select: function( event, ui ) {
		//muestra el botón modificar  
		$(".modificar").show();
		$(".guardar").hide();
		
		//da el nombre del formulario para buscarlo en el DOM
		form=ui.item.form;
		
		//asigna el valor en el campo
		$.each(ui.item,function(i,v){
			selector=form+" ."+i
			$(selector).val(v);
		});
		datosContacto(ui.item.id_cliente,'clientes');
		datosFiscal(ui.item.id_cliente,'clientes');
	  }
    });

	//inhabilita los campos con la clase deshabilitar
	$(".disabled").attr("disabled","disabled");
	
	//quita el atributo name
	$(".remueve_name").removeAttr("name");
	
    $(".volver").click(function(e) {
		ingresar=true;
    	$("#formularios_modulo").hide("slide",{direction:'right'},rapidez,function(){
			$("#botones_modulo").fadeIn(rapidez);
		});
    });
	
	$("#f_proveedores select").change(function(e) {
		campo="."+$(this).attr("data-campo");
		$(campo).val($(this).val());
		clave=$('option:selected', this).attr("data-clave");
		nombre=$('option:selected', this).attr("data-nombre");
		$(".clave").val(clave);
		$(".nombre").val(nombre);
    });
	
	
	$(".guardar").click(function(e) {
	  if(requerido()){
		//metodo en que se va a guardar
		metodo=$(this).attr('data-m');
		
		//datos de los formularios
		quitar=$("input[name=quitar]");
		quitar.removeAttr("name");
		
		//datos de los formularios
		datos={};
        $.each($("form"),function(i, v){
			datos[$(this).attr("id")]=$(this).serializeArray();
		});
		
		quitar.attr("name","quitar");
		console.log(datos);
		
		//procesamiento de datos
		$.ajax({
			url:'scripts/s_guardar_form.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'metodo':metodo,
				'datos':datos
			},
			success: function(r){
				if(r.continuar){
					alerta("info",r.info,function(){
						
					});
				}else{
					alerta("error",r.info);
				}
			}
		});
	  }//if del requerido*/
    });
	
	$(".modificar").click(function(e) {
	  if(requerido()){
		
		quitar=$("input[name=quitar]");
		quitar.removeAttr("name");
		
		//datos de los formularios
		datos={};
        $.each($("form"),function(i, v){
			datos[$(this).attr("id")]=$(this).serializeArray();
		});
		
		quitar.attr("name","quitar");
		console.log(datos);
		
		//procesamiento de datos
		$.ajax({
			url:'scripts/s_modificar_form.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'datos':datos
			},
			success: function(r){
				if(r.continuar){
					alerta("info",r.info,function(){
						$(".volver").click();
					});
				}else{
					alerta("error",r.info);
				}//*/
			}
		});
	  }//if del requerido
    });
	
	$(".guardar_individual").click(function(e) {
	  if(requerido()){
		//metodo en que se va a guardar
		metodo=$(this).attr('data-m');
		tabla=$(this).parent().parent().attr("id");
		forma="#"+$(this).parent().parent().attr("id");
		
		//siempre usar este método cuando se vaya a utilizar el serialize + otros campos
		datos={};
		$.each($(forma).serializeArray(),function(i,v){
			//console.log(v["name"]+" "+v["value"]);
			datos[v["name"]]=v["value"];
		});
		
		//procesamiento de datos
		$.ajax({
			url:'scripts/s_guardar_form.php',
			cache:false,
			async:false,
			type:'POST',
			data:{
				'metodo':metodo,
				'tabla':tabla,
				'datos':datos
			},
			success: function(r){
				if(r.continuar){
					alerta("info",r.info,function(){
						$(".volver").click();
					});
				}else{
					alerta("error",r.info);
				}
			}
		});
	  }//if del requerido
    });
	
/*Sección de articulos*/
	$(".id_area").change(function(){
		id=$(this).val();
		$(".id_familia").html('');
		$(".id_subfamilia").html('');
		$.ajax({
			url:'scripts/busca_familias.php',
			cache:false,
			type:'POST',
			data:{
				id:id
			},
			success: function(r){
				$(".id_familia").html(r);
			}
		});
	});
	$(".id_familia").change(function(){
		id=$(this).val();
		$(".id_subfamilia").html('');
		$.ajax({
			url:'scripts/busca_subfamilias.php',
			cache:false,
			type:'POST',
			data:{
				id:id
			},
			success: function(r){
				$(".id_subfamilia").html(r);
			}
		});
	});

});
function requerido(){
	selector=".requerido";
	continuar=true;
	$.each($(selector).parent().find(".requerido"),function(i,v){
		if($(this).val()==""){
			$(this).addClass("falta_llenar");
			continuar=false;
		}
	});
	return continuar;
}
function datosContacto(id,tabla){
	$.ajax({
		url:'scripts/s_datos_contacto.php',
		cache:false,
		type:'POST',
		data:{
			'id':id,
			'tabla':tabla
		},
		success: function(r){
			form=r.form
			$.each(r,function(i,v){
				//console.log();
				selector=form+" ."+i;
				$(selector).val(v);//*/
			});
		}
	});
}
function datosFiscal(id,tabla){
	$.ajax({
		url:'scripts/s_datos_fiscal.php',
		cache:false,
		type:'POST',
		data:{
			'id':id,
			'tabla':tabla
		},
		success: function(r){
			form=r.form
			$.each(r,function(i,v){
				//console.log();
				selector=form+" ."+i;
				$(selector).val(v);//*/
			});
		}
	});
}
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