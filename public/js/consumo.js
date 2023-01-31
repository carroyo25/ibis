$(function(){
    $("#espera").fadeOut();

    let row = ``;
    $("#docident").focus;

    $("#docident").keypress(function (e) { 
        if(e.which == 13) { 
            $.post(RUTA+"consumo/datosapi",{documento:$(this).val()},
                function (data, textStatus, jqXHR) {
                    if (data.registrado) {
                        $("#nombre").val(data.datos[0].paterno+' '+data.datos[0].materno+' '+data.datos[0].nombres);
                        $("#cargo").val(data.datos[0].cargo.toUpperCase());
                        $("#cut").val(data.datos[0].cut);
                        $("#correo").val(data.datos[0].correo);
    
                        $("#codeRead").focus();
                    }else{
                        mostrarMensaje("Trabajador no registrado","mensaje_error");
                        $("#nombre, #cargo, #cut, #correo").val("");
                    }
                   
                },
                "json"
            );
        }
    });

    $("#codeRead").keypress(function (e) { 
        if(e.which == 13) {
            $.post(RUTA+"consumo/productos", {codigo:$(this).val()},
                function (data, textStatus, jqXHR) {
                    let fecha = fechaActual();
                    let nfilas = $("#tablaPrincipal tr").length;

                    console.log(data);

                    var row = `<tr data-registrado=0 class="pointer" data-idprod="'${data.idprod}'">
                                <td class="textoDerecha">${nfilas}</td>
                                <td class="textoCentro">${data.codigo}</td>
                                <td class="pl20px">${data.descripcion}</td>
                                <td class="textoCentro">${data.unidad}</td>
                                <td class=""><input type="text" value=1 class="textoDerecha unstyled entrada" onClick="this.select();"></td>
                                <td class=""><input type="date" class="unstyled textoCentro entrada" value="${fecha}"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class=""><input type="text" class="entrada"></td>
                                <td class="textoCentro"><a href=""><i class="far fa-trash-alt"></i></a></td>
                        </tr>`;

                    if (data.registrado) {
                        $("#tablaPrincipal tbody").append(row);
                        $('#tablaPrincipal tbody tr:last').find('td').eq(4).children().focus();
                    }
                },
                "json"
            );
         }
    });

    $("#btnFirma").click(function(e){
        e.preventDefault();

        $("#comentarios").fadeIn();
        
        return false
    });
})