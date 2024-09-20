$(function(){

	let nroRegistro = 0;
	
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

	$("body").on("focusOut","#cabecera_menu", function (e) {
		e.preventDefault();

		$("#cabecera_menu").fadeOut();

		return false;
	});

	$("body").on("click","#irInicio", function (e) {
		e.preventDefault();

		window.location = RUTA + "panel";

		return false;
	});

	$("#changePass").click(function(e){
		e.preventDefault();

		$("#cambio").fadeIn();
		$("#cabecera_menu").fadeOut();

		return false;
	});

	$("#btnCancelarCambio").click(function(e){
		e.preventDefault();

		$("#cambio").fadeOut();

		return false;
	});

	$("#btnAceptarCambio").click(function(e){
		e.preventDefault();
		try {

			if ( $("#nueva_clave").val() === "") throw "Ingrese una clave";
			if ( $("#nueva_clave_comfirm").val() === "") throw "Confirme la clave ingresada";
			if ( $("#nueva_clave").val() !== $("#nueva_clave_comfirm").val()) throw "Las claves no son iguales";

			$.post(RUTA+"panel/cambiaClave", {clave:$("#nueva_clave").val()},
				function (data, textStatus, jqXHR) {
					$("#cambio").fadeOut();
					if (data) {
						mostrarMensaje("Clave cambiada","mensaje_correcto");
						$("#form__clave")[0].reset();
					}else {
						mostrarMensaje("No se actualizo la clave","mensaje_error");
					}	
				},
				"text"
			);
			
			
		} catch (error) {
			mostrarMensaje(error,'mensaje_error');
		}
		

		return false;
	});

	$("#tablaPanelAsignaciones tbody").on("click","a", function (e) {
		e.preventDefault();
  
		nroRegistro = $(this).attr("href");

		$("#preguntaVerifica").fadeIn();
  
		return false;
	  });
  

	$("#btnAceptarVerifica").click(function(e) {
		e.preventDefault();

		let formData = new FormData();
		formData.append("id",nroRegistro);
		formData.append("user",$("#id_user").val());

		fetch(RUTA+"panel/marcaRegistro",{
			method: "POST",
			body: formData
		})
		.then(response => response.text())
		.then(data => {
			$("#tablaPanelAsignaciones tbody")
				.empty()
				.append(data);
			$("#preguntaVerifica").fadeOut();
		})
	
		return false;
	})
	
	
	$("#btnCancelarVerifica").click(function(e) {
		e.preventDefault();
	
		$("#pregunta").fadeOut();
	
		return false;
	})
})