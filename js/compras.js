// JavaScript Document
$(document).ready(function(e) {
	$(".comprar").click(function(e) {
		id=$(this).attr("data-id");
		if(!$("#wrap_forma_"+id).is(":hidden")){
			$("#wrap_forma_"+id).slideUp(200);
		}else{
			$(".formacompra").hide();
			$("#wrap_forma_"+id).slideDown(200);
		}
    });
	$(".cancelar").click(function(e) {
        id=$(this).attr("data-id");
		$.ajax({
			url:'scripts/s_cancelar_orden.php',
			cache:false,
			type:'POST',
			data:{
				id:id
			},
			success: function(r){
				if(r.continuar){
					alerta("info","Orden de compra cancelada");
					//se elimina de la lista
					$("tr#compra"+id).remove();
				}else{
					alerta("error",r.info);
				}
			}
		});
    });
	$(".hecho").click(function(e) {
		_this=$(this);
		_this.hide();
		requerido=true;
		tr=$(this).attr("data-boton");
		generar=$(tr+" .comprar");
		cancelar=$(tr+" .cancelar");
		campos="";
		f=$(this).attr("data-form");
		//datos requeridos
		$.each($(f).find(".requerido"),function(i,v){
			v=$(this).val();
			if(v==""){
				campo=$(this).attr("data-campo");
				campos=campos+campo+', ';
				requerido=false;
			}
		});
		if(requerido){
			datos=$(f).serialize();
			$.ajax({
				url:'scripts/s_proc_compra.php',
				cache:false,
				type:'POST',
				data:datos,
				success: function(r){
					if(r.continuar){
					alerta("info",r.info);
					//remueve el formulario
					$(f).parent().remove();
					//cambiamos el estatus
					$(tr).find(".estatus").html('terminada');
					//remueve el botón de generar entrada y cancelar
					generar.remove();
					cancelar.remove();
					}else{
						alerta("error",r.info);
					}
				}
			});//*/
		}else{
			setTimeout(function(){_this.show();},1000);
			alerta("error","Falta elegir un dato en: "+campos);
		}
    });
	
	//para mostrar los bancos
	$(".pagopor").change(function(){
		$(".bancos select").val('');
		if($(this).val()=="efectivo"){
			$(".bancos").hide();
		}else{
			$(".bancos").show();
		}
	});
	
	//para modificar totales
	$.each($(".f_compra"),function(i,v){
		total=0;
		id="#"+$(this).attr("id");
		$.each($(this).find(".monto"),function(ii,vv){
			total+=$(this).val()*1;
		});
		$(id+" .totalcompra").val(total);
	});
	
	$(".monto").keyup(function(e) {
        if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode>=96 && e.keyCode<=105)){
			totalCompra(this);
		}
    });
	
	//termina para modificar totales
});//termina document ready
function totalCompra(e){
		total=0;
		id=$(e).attr("data-form");
		$.each($(id).find(".cantidad"),function(ii,vv){
			total+=$(this).val()*1;
		});
		$(id+" .totalcompra").val(total);
}