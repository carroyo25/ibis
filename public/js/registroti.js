$(function(){
    
    $("#esperar").css({"display":"none","opacity":"0"});

    let fila = "",
        registro = 0,
        sw = 0,
        codigo="",
        idprod="",
        descripcion="",
        und = "";

        $("#espera").fadeOut();

        $("#docident").focus;

        $("#docident").keypress(function (e) { 
            if(e.which == 13) { 
                $.post(RUTA+"consumo/datosapi",{documento:$(this).val(),costos:$("#costosSearch").val()},
                    function (data, textStatus, jqXHR) {
                        if (data.registrado) {
                            $("#nombre").val(data.datos[0].paterno+' '+data.datos[0].materno+' '+data.datos[0].nombres);
                            $("#cargo").val(data.datos[0].cargo.toUpperCase());
                            $("#cut").val(data.datos[0].cut);
                            $("#correo").val(data.datos[0].correo);
    
                            $("#tablaPrincipal tbody")
                                .empty()
                                .append(data.anteriores);
                        }else{
                            mostrarMensaje("Trabajador no registrado","mensaje_error");
                            $("#nombre, #cargo, #cut, #correo").val("");
                        }
                    },
                    "json"
                );
            }
        });

        $("#nombre").keypress((e)=>{
            if(e.which == 13){
                let formData = new FormData();

                formData.append('nombre',$("#nombre").val());
                formData.append('costos',$("#costosSearch").val());

                fetch(RUTA+'registroti/datosNombre',{
                    method:'POST',
                    body:formData
                })
                .then(response => response.json())
                .then(data =>{
                    if (data.registrado) {
                        $("#nombre").val(data.datos[0].paterno+' '+data.datos[0].materno+' '+data.datos[0].nombres);
                        $("#cargo").val(data.datos[0].cargo.toUpperCase());
                        $("#cut").val(data.datos[0].cut);
                        $("#correo").val(data.datos[0].correo);
                        $("#docident").val(data.datos[0].dni);
    
                        $("#tablaPrincipal tbody")
                            .empty()
                            .append(data.anteriores);
                    }else{
                        mostrarMensaje("Trabajador no registrado","mensaje_error");
                        $("#nombre, #cargo, #cut, #correo, #docident").val("");
                    }
                })
            }
        })

        $("#btnAceptarDialogoKardex").click(function (e) { 
            e.preventDefault();
    
            try {
                if(idprod === "") throw new Error("Elija un producto para registrar");
                if($("#cantidad_dialogo").val() =="") throw new Error("Ingrese una cantidad para registrar");
                if($("#serie_dialogo").val() =="") throw new Error("Debe registrar la serie del usuario");
    
                let 
                    cant            = $("#cantidad_dialogo").val(),
                    fsalida         = fechaActual(),
                    observaciones   = $("#observaciones_dialogo").val(),
                    serie           = $("#serie_dialogo").val(),
                    patrimonio      = $("#patrimonio").prop('checked'),
                    nfilas          = $("#tablaPrincipal tr").length,
                    textoSelect     = "",
                    pat             = "";
    
                    pat = patrimonio === true ? '<i class="far fa-check-square"></i>' : '<i class="far fa-square"></i>';
    
                let row = `<tr data-registrado=0 class="pointer" data-idprod="${idprod}" data-cambio="${cambio}" data-patrimonio="${patrimonio}">
                                <td class="textoDerecha">${nfilas}</td>
                                <td class="textoCentro">${codigo}</td>
                                <td class="pl20px">${descripcion}</td>
                                <td class="textoCentro">${und}</td>
                                <td class=""><input type="text" value="${cant}"></td>
                                <td class=""><input type="date" class="unstyled textoCentro entrada" value="${fsalida}"></td>
                                <td class=""></td>
                                <td class=""></td>
                                <td class=""><input type="text" class="entrada" value="${observaciones}"></td>
                                <td class=""><input type="text" class="entrada" value="${serie}"></td>
                                <td class="textoCentro">${pat}</td>
                                <td class="">${textoSelect}</td>
                                <td class=""></td>
                                <td class=""></td>
                                <td class="textoCentro"><a href=""><i class="far fa-trash-alt"></i></a></td>
                        </tr>`;
             
                //codigos para mantenimiento
                let formData = new FormData();

                formData.append('codigo',idprod);
                formData.append('serie',serie);
                formData.append('documento',$("#docident").val());
                formData.append("costos",$("#costosSearch").val());
                
                fetch(RUTA+'consumo/mantenimientos',{
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data=>{
                   if (data.respuesta){
                        mostrarMensaje("La seria ya esta registrada","mensaje_error");
                   }else{
                        if ( $("#tablaPrincipal tbody tr").length == 0)  {
                            $("#tablaPrincipal tbody").append(row);
                        }
                        else {
                            $(row).insertBefore("#tablaPrincipal tbody tr:first");
                        } 
            
                        cleanDialogControls();
                        idprod="";
                   }
                })
            } catch (error) {
                mostrarMensaje(error,"mensaje_error")
            }
    
            return false;
        });
    
        $("#btnCancelarDialogoKardex").click(function (e) { 
            e.preventDefault();
    
            $("#dialogo_registro").fadeOut();
            
            cleanDialogControls();
            
            idprod="";
            
            return false;
        });

        $("#btnRegister").click(function(e){
            e.preventDefault();
    
                $.post(RUTA+"registroti/buscaCatalogo", {tipo:37},
                    function (data, textStatus, jqXHR) {
                        $("#tabla_detalles_productos tbody")
                            .empty()
                            .append(data);
    
                            $("#dialogo_registro").fadeIn();
    
                    },
                    "text"
                );
    
            
            return false;
        });
    
        //filtrar Item del pedido
        $("#codigoSearch, #descripSearch").on("keypress", function (e) {
            if(e.which == 13) {
                $("#esperar").fadeIn();
                
                $.post(RUTA+"registroti/filtraItemsTi", {codigo:$("#codigoSearch").val(),
                                                    descripcion:$("#descripSearch").val(),
                                                    tipo:37},
                        function (data, textStatus, jqXHR) {
                            $("#tabla_detalles_productos tbody")
                                .empty()
                                .append(data);
                            $("#esperar").fadeOut();
                        },
                        "text"
                    );
            }
        });
    
        $("#tabla_detalles_productos tbody").on('click','tr', function(e) {
            e.preventDefault();
    
            idprod = $(this).data("idprod");
            codigo = $(this).find('td').eq(0).text();
            descripcion = $(this).find('td').eq(1).text();
            und = $(this).find('td').eq(2).text();
    
            $(this).toggleClass('semaforoNaranja');
    
            return false;
        });

        $("#btnGrabarKardex").click(function(e){
            e.preventDefault();
    
            try {
                if ( $("#costosSearch").val() == -1 ) throw "Elija el centro de costos";
                if ( $("#docident").val() == "" ) throw "Indique el N° de documento";
                if ( $("#tablaPrincipal tbody tr").length == 0 ) throw "No relleno productos";
    
                $("#pregunta").fadeIn();
    
            } catch (error) {
                mostrarMensaje(error,"mensaje_error");
            }
    
            return false
        });

        $("#btnAceptarGrabar").click(function (e) { 
            e.preventDefault();
    
            //para deasctivar el doble click de mouse
            let boton = $(this);
            
            boton.css("pointer-events","none");
    
            let canvas = document.getElementById("cnv");
    
            $.post(RUTA+'registroti/firmaTi', {img:canvas.toDataURL(),
                                          detalles:JSON.stringify(detalles()),
                                          correo:$("#correo").val(),
                                          nombre:$("#nombre").val(),
                                          cc:$("#costosSearch option:selected").text()},
                function (data, textStatus, jqXHR) {
                    if (data) {
                        mostrarMensaje("Consumo registrado","mensaje_correcto");
                        
                        $("#tablaPrincipal tbody").empty();
                        $("#costosSearch").val(-1);
                        $(".ingreso").val("");
    
                        boton.css("pointer-events","all");
                        
                    }else {
                        mostrarMensaje("Hubo un error al grabar","mensaje_error");
                    }
    
                    $("#pregunta").fadeOut();
                },
                "text"
            );
            
            return false;
        });
    
        $("#btnCancelarGrabar").click(function (e) { 
            e.preventDefault();
    
            $("#tablaPrincipal tbody").empty();
            $("#costosSearch").val(-1);
            $(".ingreso").val("");
    
            $("#pregunta").fadeOut();
            
            return false;
        });
})

cleanDialogControls = () => {
    $("#codigoSearch").val("");
    $("#descripSearch").val("");
    $("#patrimonio").prop("checked", true);
    $("#cantidad_dialogo").val("");
    $("#serie_dialogo").val("");
    $("#observaciones_dialogo").val("");
}

detalles = () => {
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            IDPROD      = $(this).data("idprod"),
            GRABADO     = $(this).data("grabado"),
            CODIGO      = $(this).find('td').eq(1).text(),
            DESCRIPCION = $(this).find('td').eq(2).text(),
            UNIDAD      = $(this).find('td').eq(3).text(),
            CANTIDAD    = $(this).find('td').eq(4).children().val(),
            FECHA       = $(this).find('td').eq(5).children().val(),
            OBSERVAC    = $(this).find('td').eq(8).children().val(),
            SERIE       = $(this).find('td').eq(9).children().val(),
            ESTADO      = $(this).find('td').eq(12).text(),
            COSTOS      = $("#costosSearch").val(),
            NRODOC      = $("#docident").val(),
            PATRIMONIO  = $(this).data("patrimonio"),
            CAMBIO      = $(this).data("cambio");


        item = {};
        
        if (!GRABADO) {
            item['item']        = ITEM;
            item['codigo']      = CODIGO;
            item['descripcion'] = DESCRIPCION;
            item['unidad']      = UNIDAD;
            item['cantidad']    = CANTIDAD;
            item['fecha']       = FECHA;
            item['observac']    = OBSERVAC;
            item['patrimonio']  = PATRIMONIO;
            item['costos']      = COSTOS;
            item['nrodoc']      = NRODOC;
            item['idprod']      = IDPROD;
            item['serie']       = SERIE;
            item['cambio']      = CAMBIO;

            DATA.push(item);
        }
    })

    return DATA;
}