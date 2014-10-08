//javascript para index
$(document).ready(function(e) {
    $(".login").click(function(e) {
		$(".respuesta").html('<img src="img/loading.gif" />');
        datos=$("#login").serialize();
		$.ajax({
			url:'scripts/login.php',
			cache:false,
			type:'POST',
			data:datos,
			success: function(r){
				$(".respuesta").html(r);
			}
		});
    });
	$("input").keyup(function(e) {
        if(e.keyCode==13){
			$(".login").click();
		}
    });
});