$(function(){
	

    $(".acordeon .submenu").on("click","a", function (e) {
        e.preventDefault();

        $(".opcion").removeClass("visitado");
        $(this).addClass("visitado");
		
		$("#esperar").fadeIn();
        
		$(".cargaModulo").load($(this).attr("href"),function(){
			
		});

        return false;
    });

    $(".acordeon").on("click",".link", function (e) {
		e.preventDefault();

        $(".submenu").slideUp();

		if (open != $(this).text() ) {
			$(this).next(".submenu").slideToggle();
			$(this).children(".fa-chevron-down").css("transform","rotate(180deg)");
			open = $(this).text()
		}else{
			$(this).next(".submenu").slideToggle();
			$(this).children(".fa-chevron-down").css("transform","rotate(0)");
		}

		return false;
	});

	$("#cabecera_main_option").click(function (e) { 
		e.preventDefault();
		
		$("#cabecera_menu").slideToggle();

		return false;
	});

	$("body").on("click","#irInicio", function (e) {
		e.preventDefault();

		window.location = RUTA + "panel";

		return false;
	});
})