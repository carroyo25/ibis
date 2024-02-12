$(function(){
    $("#esperar").fadeOut();

    $("#btnConsulta").on('click', function(e) {
        e.preventDefault();

        $("#esperar").css("opacity","1").fadeIn();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"valorizado/consulta", str,
            function (data, text, requestXHR) {
                $("#esperar").css("opacity","0").fadeOut();
                $("#tableValorizado tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
        
        return false
    });

    $("#btnExporta").click(function(e){
        e.preventDefault();

        var array = [];
        /* Obtenemos todos los tr del Body*/
        var rowsBody= $("#tableValorizado").find('tbody > tr');
        /* Obtenemos todos los th del Thead */
        var rowsHead= $("#tableValorizado").find('thead > tr > th');
        
        /* Iteramos sobre as filas del tbody*/
        for (var i = 0; i < rowsBody.length; i++) {
            var obj={};/* auxiliar*/
            for (var j = 0;j < rowsHead.length;j++) /*  Iteramos sobre los th de THead*/
                /*Asignamos como clave el text del th del thead*/
                /*Asignamos como Valor el text del tr del tbody*/
                obj[rowsHead[j].dataset.titulo] =  rowsBody[i].getElementsByTagName('td')[j].innerText;
            
            array.push(obj);/* Añadimos al Array Principal*/
        }

        $.post(RUTA+"valorizado/exportar", {detalles:JSON.stringify(array)},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
            },
            "json"
        );

        return false;
    });

    $("#downFiles").click(function(e){
        e.preventDefault();

        try {
            if  ( $("#costosSearch").val() ===  "-1" ) throw "Seleccione un centro de costos";

            
            let row = "",
            formData = new FormData();
            formData.append("cc",$("#costosSearch").val());
            formData.append("anio",$("#anioSearch").val());

            $("#esperar").css("opacity","1").fadeIn();

            fetch(RUTA+'valorizado/adjuntosCarpeta',{
                method:'POST',
                body:formData
            })
            .then(response => response.json())
            .then(data => {
                data.ordenes.forEach(element => {
                    let tipo = element.ntipmov == 37 ? 'OC':'OS';
                    row +=`<li>
                                <a href="#" data-id="${element.id_regmov}" title="${element.cObservacion}">
                                    <i class="fas fa-folder" style="color: #FFD43B;"></i>
                                    <p>${tipo}: ${element.cnumero}</p>
                                </a>
                            </li>`;
                    
                    $(".listaAdjuntos ul").empty().append(row);    
                });

                $("#esperar").css("opacity","0").fadeOut();
                $("#vistaAdjuntos").fadeIn();
            })
            
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');  
        }

        return false;
    });

    /*$("#downFiles").click(function (e) { 
        e.preventDefault();

        try {
          if  ( $("#costosSearch").val() ===  "-1" ) throw "Seleccione un centro de costos";

          
          let row = "";

          $.post(RUTA+"valorizado/adjuntos",{cc:$("#costosSearch").val()},
            function (data, textStatus, jqXHR) {
                data.adjuntos.forEach(adjunto => {
                    row +=` <li>
                                <a href="public/documentos/ordenes/adjuntos/${adjunto.creferencia}" title="${adjunto.mensaje}" target="_blank" rel="noopener noreferrer" >
                                    <i class="fas fa-file"></i>
                                    <p>Orden N°: ${adjunto.orden}</p>
                                    <p>${adjunto.documento}</p>
                                </a>
                            </li>`;
                });

                $(".listaAdjuntos ul").empty().append(row);
            },
            "json"
          );

          $("#vistaAdjuntos").fadeIn();

        } catch (error) {
            mostrarMensaje(error,'mensaje_error');  
        }

        
        return false;
    });*/

    $("#closeAtach").click(function (e) { 
        e.preventDefault();

        $(".ventanaAdjuntos iframe").attr("src","");
        $("#vistaAdjuntos").fadeOut();

        return false;
    });

    /*$(".listaAdjuntos").on("click","a", function (e) {
        e.preventDefault();



        return false;
    });*/
})