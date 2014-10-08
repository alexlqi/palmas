// JavaScript Document
var focusIn="";
var sesid="";
var tml; //timer mouse leave
barraprogreso=$('<table border="0" style="width:100%;height:100%;"><tr><td class="barraprogreso" valign="middle"><img src="img/site/barraprogreso.gif" /></td></tr></table>');

$(document).ready(function(e) {
	url=document.URL;
	$('.fancybox').fancybox();
	//Zona de carga de contenidos
	cargarContenido($("#redes"),"parts/unete.php?url="+document.URL);
	
	$('#regis').click(function(e) {
		$.ajax({
			url:'forms/usuarios.php',
			cache:false,
			type:'GET',
			success: function(r){
				$("#nuevo").html(r);
				setTimeout(function(){
					$('#wrap_reg').fadeIn(300);
				},500);
			}
		});
    });

	//Feeders para contenidos
	feeder("#slider ul");
	feeder("#media ul");
	//*/
	
	//funciones para el modulo de buscar
	$('#buscar').on('keyup',function(e){
		cadena=$("#buscar").val();
		if(e.keyCode==8){
			$("#sugerencias").hide(200);
			$("#buscar").attr("data-info","");
			$("#sugerencias_restaurantes").html('');
		}
		if((e.keyCode>64 && e.keyCode<91)||e.keyCode==192){
			if(cadena!=""){
				//sugerencias de comida
				$.ajax({
					url:"scripts/s_buscador_restaurantes.php",
					cache:false,
					type:'POST',
					data:{
						c:cadena,
					},
					success: function(r){
						$('#sugerencias_restaurantes').html('');
						$('#sugerencias_restaurantes').append(r);
						$('.sugerencia').first().addClass("focus");
						$('.sugerencia').unbind('click');
						$('.sugerencia').click(function(e) {
                            $("#buscar").val($(this).html());
							$("#buscar").attr("data-info",$(this).attr("data-id-info"));
							setTimeout(function(){
								window.location="menu.php?r="+$("#buscar").attr("data-info");
							},50);
                        });
					}				
				});
				$("#sugerencias").slideDown(200);
			} else {
				$('#sugerencias_restaurantes').html('');
				$('#sugerencias_comidas').html('');
				$("#sugerencias").hide(200);
			}//*/
		}
		if(e.keyCode==38){
			actual=$(".focus");
			previo=actual.prev().focus();
			if(previo.hasClass('sugerencia')){
				actual.removeClass('focus');
				previo.addClass('focus');
			}
		} else if(e.keyCode==40){
			actual=$(".focus");
			siguiente=actual.next().focus();
			if(siguiente.hasClass('sugerencia')){
				actual.removeClass('focus');
				siguiente.addClass('focus');
			}
		}
		if(e.keyCode==13){
			if($("#buscar").attr("data-info")==""){
				$("#buscar").val($(".focus").html());
				$("#buscar").attr("data-info",$(".focus").attr("data-id-info"));
				$("#sugerencias").slideUp(100);
			} else {
				window.location="menu.php?r="+$("#buscar").attr("data-info");
			}
		}
	});
	//Termina sección para buscar*/
	
    $(".fotolink").click(function(e) {
        
    });
	$('#colonia,#favoritos,#nose').click(function(e){
		$('#colonia,#favoritos,#nose').css('color','#6D6D6D');
		$('.pg').removeClass("pgSelcted");
		$(this).find("span").addClass("pgSelcted");
		$(this).css('color','#62A60A');
		switch($(this).attr('id')){
			case"colonia":
			$.ajax({
				url:"scripts/s_colonia.php",
				cache:false,
				success: function(r){
					$("#buscador_rapido").html(r);
				}
			});
			break;
		}
	});
	$("#colonia").click();
	
	//Zona de pop ups
	$('#login').on('mouseenter',function(){
		clearInterval(tml);
		if($('#login').attr('data-vez')!='0'){
			$.ajax({
				url:'forms/login.html',
				cache:true,
				async:false,
				success: function(r){
					$('#loginPopup').html(r);
					$('#login').attr('data-vez','0');
				}
			});
			$('#loginPopup').show();
		} else {
			$('#loginPopup').show();
		}
	});
	$('#loginPopup').mouseenter(function(e) {
        clearInterval(tml);
    });
	$('#reg').on('mouseenter',function(){
		clearInterval(tml);
		if($('#login').attr('data-vez')!='0'){
			$.ajax({
				url:'forms/login.html',
				cache:true,
				async:false,
				success: function(r){
					$('#loginPopup').html(r);
					$('#login').attr('data-vez','0');
				}
			});
			$('#loginPopup').show();
		} else {
			$('#loginPopup').show();
		}
		$('#regPopup').show();
	});
	$('#login').click(function(e){
		if($('#loginPopup').is(':visible')){
			$('#loginPopup').hide();
		} else {
			$('#loginPopup').show();
		}
	});
	$('#loginPopup, #topbar, #reg').on('mouseleave',function(){
		clearInterval(tml);
		tml=setTimeout(function(){
		$('#regPopup').hide();
		$("#r").hide();
		$(".loginWFB").show();
		$('#loginPopup').hide();
		},500);
	});
	$(".close").on("click",function(){
		closeWindow($(this));
	});
	
	//logout
	$("span").on("click",function(e){
		switch($(this).attr("data-action")){
			case "logout":
				window.location = "/forms/scripts/logout.php";
			break;
	}});
	$("#fullscreen").click(function(){
		wrap=$("#pedirWrap");
		fs=wrap.attr('data-fs');
		botones=$("#botones");
		opciones=$("#opciones");
		if(fs=="n"){
			botones.width('20%');
			opciones.width('80%');
			wrap.animate({'width':'100%'},300);
			wrap.attr('data-fs','s');
		}else{
			botones.width('30%');
			opciones.width('70%');
			wrap.animate({'width':'740px'},300);
			wrap.attr('data-fs','n');
		}
		
	})
	
});
function locSuccess(location){
	lat=location.coords.latitude;
	lng=location.coords.longitude;
	alert("Disculpa las molestias. \n Estamos trabajando por ti para habilitar esta característica");
}

function feeder(lugar, seccion){
	$(lugar).append('<li><img class="fotolink" data-url="youtube>2 video" src="img/site/b1.jpg" title="Ganamenu.com"></a></li><li><img class="fotolink" data-url="youtube>2 video" src="img/site/b2.jpg" title="Ganamenu.com"></a></li><li><img class="fotolink" data-url="youtube>video 3" src="img/site/b3.jpg" title="Ganamenu.com"></a></li><li><img class="fotolink" data-url="youtube>2 video" src="img/site/b4.jpg" title="Ganamenu.com"></a></li>');	
}

function inputFocus(objeto){
	padre=objeto.parent();
	focusIn=padre.attr('id');
}

function closeWindow(objeto){
	$(objeto.attr("data-window")).hide();	
}

function cargarContenido(lugar,url){
	lugar.load(url);
}