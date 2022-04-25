$(function() {
    var tabActive = "tab1";
    var accion = "";
    var index = "";

    $.post(RUTA+"proveedores/obtenerValores", {bancos:"02",tipo:"03"},
            function (data, textStatus, jqXHR) {
                bancos = data.bancos;
                monedas = data.monedas;
            },
            "json"
    );

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

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

        if (tabActive == "tab1") {
            let cfilas = $.strPad($("#contactos tr").length,2);
            let row = `<tr data-grabado="0">
                            <td class="textoCentro"><a href="#"><i class="far fa-trash-alt"></i></a></td>
                            <td class="textoCentro">${cfilas}</td>
                            <td><input type="text" class="mayusculas pl20px"></td>
                            <td><input type="text" class="mayusculas pl20px"></td>
                            <td><input type="text" class="minusculas pl20px"></td>
                            <td class="textoCentro"><input type="checkbox"></td>
                        </tr>`;
            
            $("#contactos tbody").append(row);
        }else if(tabActive == "tab2"){
            let bfilas = $.strPad($("#bancos tr").length,2);
            let row = `<tr data-grabado="0">
                            <td class="textoCentro"><a href="#"><i class="far fa-trash-alt"></i></a></td>
                            <td class="textoCentro">${bfilas}</td>
                            <td><select type="text" class="pl20px">${bancos}</select></td>
                            <td><select type="text" class="pl20px">${monedas}</select></td>
                            <td><input type="text" class="minusculas pl20px"></td>
                            <td class="textoCentro"><input type="checkbox"></td>
                        </tr>`;
            
            $("#bancos tbody").append(row);
        }

        return false;
    });

    $("#saveItem").click(function (e) { 
        e.preventDefault();
        
        let result = {};

        $.each($("#formProceso").serializeArray(),function(){
            result[this.name] = this.value;
        })

        try {
            if (result['razon'] == '') throw "Ingrese la razón social";
            if (result['codigo_documento'] == '') throw "Seleccione el tipo de documento";
            if (result['nrodoc'] == '') throw "Ingrese el numero de documento";
            if (result['direccion'] == '') throw "Ingrese la dirección";
            
            if (accion == 'n') {
                $.post(RUTA+"proveedores/nuevaEntidad", {datos:result,
                                                     contactos:JSON.stringify(obtenerContactos()),
                                                     bancos:JSON.stringify(obtenerBancos())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
                );
            }
            else {
                /*$.post(RUTA+"proyecto/modificaProyecto", {datos:result,costos:JSON.stringify(getItems())},
                    function (data, textStatus, jqXHR) {
                        mostrarMensaje(data.mensaje,data.clase);
                    },
                    "json"
                );*/
            }
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();
        
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

            if (id == "tipo_ent") {
                $("#codigo_tipo").val($(this).attr("href"));
                $("#tipo_ent").val($(this).text());
            }else if(id == "tipo_doc"){
                $("#codigo_documento").val($(this).attr("href"));
                $("#tipo_doc").val($(this).text());
            }else if(id == "pais") {
                $("#codigo_pais").val($(this).attr("href"));
                $("#pais").val($(this).text());
            }else if(id == "estado") {
                $("#codigo_estado").val($(this).attr("href"));
                $("#estado").val($(this).text());
            }

        return false;
    });

    $(".lista ul").blur(function (e) { 
        e.preventDefault();
        
        $(this).slideUp();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        /*$.post(RUTA+"proveedores/actualizaListado",
            function (data, textStatus, jqXHR) {
                $(".itemsTabla  table tbody")
                    .empty()
                    .append(data);
                
                $("#proceso").fadeOut();
            },
            "text"
        );*/

        $("form")[0].reset();
        $("#bancos tbody,#contatos tbody").empty();
        $("#proceso").fadeOut();

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"proveedores/consultaId", {id:$(this).data("id")},
            function (data, textStatus, jqXHR) {
                $("#codigo_entidad").val(data.proveedor[0].id_centi);
                $("#codigo_documento").val(data.proveedor[0].ctipdoc);
                $("#codigo_tipo").val(data.proveedor[0].ctipper);
                $("#codigo_pais").val(data.proveedor[0].ncodpais);
                $("#razon").val(data.proveedor[0].crazonsoc);
                $("#tipo_ent").val(data.proveedor[0].tipo_persona);
                $("#tipo_doc").val(data.proveedor[0].documento);
                $("#nrodoc").val(data.proveedor[0].cnumdoc);
                $("#direccion").val(data.proveedor[0].cviadireccion);
                $("#telefono").val(data.proveedor[0].ctelefono);
                $("#pais").val(data.proveedor[0].cdespais);
                $("#agente").val(data.proveedor[0].nagenret);
                $("#estado").val(data.proveedor[0].estado);
                $("#correo").val(data.proveedor[0].cemail);

                $("input[name=agente][value='"+data.proveedor[0].nagenret+"']").prop("checked",true);

                $("#contactos tbody").empty().append(data.contactos);
                $("#bancos tbody").empty().append(data.bancos);
            },
            "json"
        );
        accion = "u";
        $("#proceso").fadeIn();

        return false;
    });

    $("#tablaPrincipal tbody").on("click","a", function (e) {
        e.preventDefault();

        index = $(this).attr("href");

        $("#pregunta").fadeIn();
        
        return false;
    });

    $("#btnCancelarPregunta").click(function (e) { 
        e.preventDefault();
        
        $("#pregunta").fadeOut();

        return false;
    });

    $("#btnAceptarPregunta").click(function (e) { 
        e.preventDefault();

        $.post(RUTA+"proveedores/desactivaProveedor", {id:index},
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);

                $("#pregunta").fadeOut();
            },
            "text"
        );
        
        return false;
    });

    $("#consulta").keyup(function(){
        _this = this;
        buscar(_this); // arrow function para activa el buscador
    });
})

obtenerBancos = () =>{
    DATA = [];
    let TABLA = $("#bancos tbody > tr");

    TABLA.each(function(){
        let NOMBRE   = $(this).find('td').eq(2).children().val(),
            CUENTA   = $(this).find('td').eq(3).children().val(),
            NUMERO   = $(this).find('td').eq(4).children().val(),
            ACTIVO   = $(this).find('td').eq(5).children().prop('checked'),
            ESTADO = $(this).data('grabado');

        item= {};

        if ( ESTADO == 0 ) {
            item["nombre"] = NOMBRE;
            item["cuenta"] = CUENTA;
            item["numero"] = NUMERO;
            item["activo"] = ACTIVO;

            DATA.push(item);
        }  
    })

    return DATA;
}

obtenerContactos = () => {
    DATA = [];
    let TABLA = $("#contactos tbody > tr");

    TABLA.each(function(){
        let NOMBRE   = $(this).find('td').eq(2).children().val(),
            TELEFONO = $(this).find('td').eq(3).children().val(),
            CORREO   = $(this).find('td').eq(4).children().val(),
            ACTIVO   = $(this).find('td').eq(5).children().prop('checked'),
            ESTADO = $(this).data('grabado');

        item= {};

        if ( ESTADO == 0 ) {
            item["nombre"]      = NOMBRE;
            item["telefono"] = TELEFONO;
            item["correo"]     = CORREO;
            item["activo"]     = ACTIVO;

            DATA.push(item);
        }  
    })

    return DATA;
}