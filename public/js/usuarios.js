$(function(){
    var titulo = "Módulos";
    var tabActive = "tab1";
    var accion = "";

    $("#esperar").fadeOut();


    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("form")[0].reset();
        $("#modulos tbody,#costos tbody,#almacen tbody").empty();
        $("#proceso").fadeIn();
        accion = 'n';

        return false;
    });
   
    $(".tabs_labels").on("click","a", function (e) {
        e.preventDefault();

            $("a").removeClass("seleccionado");
            $(this).addClass("seleccionado");

            $(".tab").hide();

            actual_tab = $(this).data("tab"); 
            tab = '#'+$(this).data("tab");
            titulo = $(this).text();
            tabActive = $(this).data("tab");

            $(tab).fadeIn();

        return false;
    });

    $("#addItem").click(function (e) { 
            e.preventDefault();
            //aca se ponen los tabs
            $("#tituloBusqueda").text(titulo);
            $("#busqueda").css("z-index","5").fadeIn(); 
        
            if (tabActive == "tab1") {
                $.post(RUTA+"usuarios/modulos",
                    function (data, textStatus, jqXHR) {
                        $("#tablaModulos tbody")
                            .empty()
                            .append(data);
                    },
                    "text"
                );  
            }else if(tabActive == "tab2"){
                $.post(RUTA+"usuarios/costos",
                    function (data, textStatus, jqXHR) {
                        $("#tablaModulos tbody")
                            .empty()
                            .append(data);
                    },
                    "text"
                );
            }else if(tabActive == "tab3"){
                $.post(RUTA+"usuarios/almacen",
                    function (data, textStatus, jqXHR) {
                        $("#tablaModulos tbody")
                            .empty()
                            .append(data);
                    },
                    "text"
                );
            }
            
            return false;
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    //pasar lo datos del modulo
    $("#tablaModulos tbody").on("click",".tablaPointer", function (e) {
        e.preventDefault();

        if (tabActive == "tab1") {
            let nombre = $(this).children('td:eq(1)').text();
            let nFilas = $.strPad($("#modulos tr").length,2);
            let codigo = $(this).data("ncodmenu");
            let clase = $(this).data("tipo");
            let opcion = $(this).data("opcion");
        
            if (!checkExistTable($("#modulos tbody tr"),nombre,2)) {
                    let row = `<tr data-grabado="0" data-codigo="${codigo}" data-clase="${clase}" data-opcion="${opcion}">
                                    <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a> </td>
                                    <td class="textoCentro">${nFilas}</td>
                                    <td class="pl10px">${nombre}</td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                    <td class="textoCentro"><input type="checkbox"></td>
                                </tr>`;
                    
                    $("#modulos tbody").append(row);
            }else{
                    mostrarMensaje("Modulo asignado","mensaje_error")
            }
        }else if( tabActive == "tab2") {
            let codigo       = $(this).children('td:eq(0)').text();
            let costo        = $(this).children('td:eq(1)').text();
            let codigo_costo = $(this).data('id');

            ///checkExistTable(tabla destino,variable de busqueda,columna donde va a buscar)
            if (!checkExistTable($("#costos tbody tr"),costo,2)){
                let row = `<tr data-grabado="0" data-codigo="${codigo_costo}">
                                <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a> </td>
                                <td class="pl10px">${codigo}</td>
                                <td class="pl10px">${costo}</td>
                            </tr>`;
                    
                    $("#costos tbody").append(row);
            }else{
                mostrarMensaje("Centro de costos, asignado","mensaje_error")
            }
        }else if( tabActive == "tab3") {
            let codigo      = $(this).children('td:eq(0)').text();
            let almacen     = $(this).children('td:eq(1)').text();
            let codigo_almacen = $(this).data('id');

            ///checkExistTable(tabla destino,variable de busqueda,columna donde va a buscar)
            if (!checkExistTable($("#almacen tbody tr"),almacen,2)){
                let row = `<tr data-grabado="0" data-codigo="${codigo_almacen}">
                                <td class="textoCentro"><a href="#"><i class="fas fa-eraser"></i></a> </td>
                                <td class="pl10px">${codigo}</td>
                                <td class="pl10px">${almacen}</td>
                            </tr>`;
                    
                    $("#almacen tbody").append(row);
            }else{
                mostrarMensaje("Almacen, asignado","mensaje_error")
            }
        }
        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").slideUp();
        $(this).next().slideDown();

        return false;
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").slideUp();

        return false;
    });

    $(".lista").on("click",'a', function (e) {
        e.preventDefault();

        let control = $(this).parent().parent().parent();
        let destino = $(this).parent().parent().parent().prev();
        let id = ""
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

            if (id == "nombre") {
                $("#cod_resp").val($(this).attr("href"));
                $("#cod_cargo").val($(this).data("cargo"));
                $("#cargo").val($(this).data("dcargo"));
            }else if(id == "nivel"){
                $("#cod_niv").val($(this).attr("href"));
            }else if(id == "estado") {
                $("#cod_est").val($(this).attr("href"));
            }

        return false;
    });

    $(".lista ul").blur(function (e) { 
        e.preventDefault();
        
        $(this).slideUp();

        return false;
    });

    $("#nombre").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#listaTrabajadores li a").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    //grabar datos
    $("#saveItem").click(function (e) { 
        e.preventDefault();

        $("#formProceso").find('.obligatorio').each(function () {
            let elemento = $(this);

            //para ver los campos obligatorios
            if ( elemento.val() == "" ) {
                mostrarMensaje("Rellene el campo " + elemento.attr("id"),"mensaje_error");
                elemento.focus();
                return false;
            }
        })

        var result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        if( accion == 'n'){
            $.post(RUTA+"usuarios/nuevoUsuario", {cabecera:result,
                                                  modulos:JSON.stringify(obtenerModulos()),
                                                  costos:JSON.stringify(obtenerCostos()),
                                                  almacenes:JSON.stringify(obtenerAlmacenes())},
                function (data, textStatus, jqXHR) {
                    
                    if (data.respuesta) {
                        $("form")[0].reset();
                        $("#modulos tbody,#costos tbody,#almacen tbody").empty();
                        mostrarMensaje("Usuario Insertado","mensaje_correcto");
                    }else {
                        mostrarMensaje(data.mensaje,"mensaje_error");
                    }
                },
                "json"
            );
        } else{
            $.post(RUTA+"usuarios/actualizaUsuario", {cabecera:result,
                                                        modulos:JSON.stringify(obtenerModulos()),
                                                        costos:JSON.stringify(obtenerCostos()),
                                                        almacenes:JSON.stringify(obtenerAlmacenes())},
                function (data, textStatus, jqXHR) {

                    if (data.respuesta) {
                        mostrarMensaje(data.mensaje,"mensaje_correcto");
                    }
                },
                "json"
                );
        }

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"usuarios/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla  table tbody")
                    .empty()
                    .append(data);
                
                $("#proceso").fadeOut();

                $(".lista").slideUp();
            },
            "text"
        );
        return false;
    });

    $(".itemsTabla table tbody").on("click","a", function (e) {
        e.preventDefault();
        accion = 'u';

        if ($(this).data("action") == "u"){
            $.post(RUTA+"usuarios/consultaUsuario", {id:$(this).attr("href")},
                function (data, textStatus, jqXHR) {

                    $("#cod_user").val(data.cabecera[0].iduser);
                    $("#cod_resp").val(data.cabecera[0].ncodper);
                    $("#cod_niv").val(data.cabecera[0].nrol);
                    $("#cod_est").val(data.cabecera[0].nestado);
                    $("#cod_cargo").val(data.cabecera[0].ccargo);
                    $("#old_pass").val(data.cabecera[0].cclave);
                    $("#usuario").val(data.cabecera[0].cnameuser);
                    $("#clave").val(data.cabecera[0].cclave);
                    $("#correo").val(data.cabecera[0].ccorreo);
                    $("#nombre").val(data.cabecera[0].nombres + " " + data.cabecera[0].apellidos);
                    $("#cargo").val(data.cabecera[0].dcargo);
                    $("#nivel").val(data.cabecera[0].nivel);
                    $("#estado").val(data.cabecera[0].estado);
                    $("#user_inic").val(data.cabecera[0].cinicial);
                    $("#desde").val(data.cabecera[0].fvigdesde);
                    $("#hasta").val(data.cabecera[0].fvighasta);

                    $("#modulos tbody").empty().append(data.modulos);
                    $("#almacen tbody").empty().append(data.almacen);
                    $("#costos tbody").empty().append(data.costos);

                    $("#proceso").fadeIn();
                },
                "json"
            );
        }else if($(this).data("action") == "d"){
            console.log("eliminar")
        }else if($(this).data("action") == "s"){
            $.post(RUTA+"usuarios/clave",{id:$(this).attr("href")} ,
                function (data, textStatus, jqXHR) {
                    $("#claveUsuario").text(data);
                    $("#dialogo").fadeIn();
                },
                "text"
            );
        }

        return false;
    });

    $("#btnAceptarDialogo").click(function (e) { 
        e.preventDefault();
        
        $("#dialogo").fadeOut();

        return false;
    });

    $("#consulta").keyup(function(){
        _this = this;
        buscar(_this); // arrow function para activa el buscador
    });

    $("#modulos tbody").on("click","a", function (e) {
        e.preventDefault();

        let parent = $(this).parent().parent();
        parent.remove();

        if (parent.data("grabado") == 1){
            $.post(RUTA+"usuarios/desactivaItem", {id:$(this).attr("href"),query:"UPDATE tb_usermod SET flgactivo = 0 WHERE ncodmod =:id"},
                function (data, textStatus, jqXHR) {
                    fillTables($("#modulos tbody > tr"),1);
                },
                "text"
            );
        } 

        return false
    });

    $("#costos tbody").on("click","a", function (e) {
        e.preventDefault();

        let parent = $(this).parent().parent();
        parent.remove();

        if (parent.data("grabado") == 1){
            $.post(RUTA+"usuarios/desactivaItem", {id:$(this).attr("href"),query:"UPDATE tb_costusu SET nflgactivo = 0 WHERE ncodcos =:id"},
                function (data, textStatus, jqXHR) {
                    fillTables($("#costos tbody > tr"),1);
                },
                "text"
            );
        } 

        return false
    });

    $("#almacen tbody").on("click","a", function (e) {
        e.preventDefault();

        let parent = $(this).parent().parent();
        parent.remove();

        if (parent.data("grabado") == 1){
            $.post(RUTA+"usuarios/desactivaItem", {id:$(this).attr("href"),query:"UPDATE tb_almausu SET nflgactivo = 0 WHERE ncodalm =:id"},
                function (data, textStatus, jqXHR) {
                    fillTables($("#almacenes tbody > tr"),1);
                },
                "text"
            );
        } 

        return false
    });
})

obtenerModulos = () =>{
    var MODULOS = [];
    var TABLA = $("#modulos tbody > tr");

    TABLA.each(function(){
        var CODMOD      = $(this).data("codigo"),
            ESTADO      = $(this).data("grabado"),
            CLASE       = $(this).data("clase"),
            OPCION      = $(this).data("opcion"),
            AGREGAR     = $(this).find('td').eq(3).children().prop("checked"),
            MODIFICAR   = $(this).find('td').eq(4).children().prop("checked"),
            ELIMINAR    = $(this).find('td').eq(5).children().prop("checked"),
            IMPRIMIR    = $(this).find('td').eq(6).children().prop("checked"),
            PROCESAR    = $(this).find('td').eq(7).children().prop("checked"),
            VISIBLE     = $(this).find('td').eq(8).children().prop("checked"),
            TODOS       = $(this).find('td').eq(9).children().prop("checked"),
            
            item={};

            if (ESTADO == '0'){

                item["codm"]  = CODMOD;
                item["clas"]  = CLASE;
                item["opci"]  = OPCION;
                item["agre"]  = AGREGAR;
                item["modi"]  = MODIFICAR;
                item["elim"]  = ELIMINAR;
                item["impr"]  = IMPRIMIR;
                item["proc"]  = PROCESAR;
                item["visi"]  = VISIBLE;
                item["todo"]  = TODOS;
                
                MODULOS.push(item);
            }    
    })

    return MODULOS;
}

obtenerCostos = () => {
    let PROYECTOS = [];
    let TABLA = $("#costos tbody > tr");

    TABLA.each(function(){
        var CODPR  = $(this).data("codigo"),
            ESTADO = $(this).data("grabado"),
            
            item={};

            if (ESTADO == '0'){

                item["codpr"]  = CODPR;
                
                PROYECTOS.push(item);
            }    
    })

    return PROYECTOS;
}

obtenerAlmacenes = () => {
    let ALMACENES = [];
    let TABLA = $("#almacen tbody > tr");

    TABLA.each(function(){
        var CODALM  = $(this).data("codigo"),
            ESTADO = $(this).data("grabado"),
            
            item={};

            if (ESTADO == '0'){

                item["codalm"]  = CODALM;
                
                ALMACENES.push(item);
            }    
    })

    return ALMACENES;
}

obtenerAprobacion = () => {}