$(function() {
    var titulo = "Módulos";
    var tabActive = "tab1";
    var accion = "";
    var bancos

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        //llama los valores para los selects
        $.post(RUTA+"proveedores/obtenerValores", {bancos:"02",tipo:"03"},
            function (data, textStatus, jqXHR) {
                bancos = data.bancos;
                monedas = data.monedas;
            },
            "json"
        );

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

    $("#tipo_ent,#tipo_doc,#pais").focus(function (e) { 
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