$(() => {

    let accion = 0;
    let fila = "";
    let idprod = 0;

    $("#esperar").fadeOut();

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();

        $(this).next().slideDown();

        return false;
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });

    $("#tablaPrincipal tbody").on("dblclick","tr", function (e) {
        e.preventDefault();

        idprod = $(this).data("idprod"); 

        $("#codigo_item").text( $(this).find('td').eq(1).text() );
        $("#descripcion_item").text( $(this).find('td').eq(2).text() );

        resumen(idprod);

        return false;
    });

    $("#tablaPrincipal tbody").on("click","a", function(e) {
        e.preventDefault();

        $(this).parent().parent().toggleClass('semaforoNaranja');

        return false;
    });

    $("#closeDocument").click(function (e) { 
        e.preventDefault();
        
        $("#vistadocumento").fadeOut();

        return false;
    });

    $("#excelFile").click(function (e) { 
        e.preventDefault();

        $("#esperar").css("opacity","1").fadeIn();

        $.post(RUTA+"stocks/exporta", {detalles:JSON.stringify(detalles())},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
                 $("#esperar").css("opacity","0").fadeOut();
            },
            "json"
        );

        return false;
        
    });

    $("#btnConsulta").click(function (e) { 
        e.preventDefault();

        let str = $("#formConsulta").serialize();
        
        let registros = vueltas(str);

        try {
            if ( $("#costosSearch").val() == -1) throw "Por favor elija un centro de costos para la consulta";

            $("#esperar").css("opacity","1").fadeIn();
            
            const progreso_accion = setInterval(progreso,1000);
        
            $.post(RUTA+"stocks/consulta",str,
                function (data, textStatus, jqXHR) {
                    $("#tablaPrincipal tbody")
                        .empty()
                        .append(data);

                        $("#esperar").css("opacity","0").fadeOut();
                        clearInterval(progreso_accion);
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        

        return false;
    });

    $("#btnAgregarMin").click(function(e){
        e.preventDefault();

        $("#registroStock").fadeIn();

        return false;
    });

    $("#btnAceptarStock").click(function(e){
        e.preventDefault();

        $.post(RUTA+"stocks/minimo", { cc:$("#costosSearch").val(),
                                        prod:idprod,
                                        cantidad:$("#stockMin").val()},
            function (data, text, requestXHR) {
                $("#registroStock").fadeOut();
            },
            "json"
        );
        
        return false;
    });

    $("#btnCancelarStock").click(function(e){
        e.preventDefault();

        $("#registroStock").fadeOut();

        return false;
    });

    $(".tab_button").click(function(e){
        $(".tab_button").addClass('tab_inactivo');
        $(this).removeClass('tab_inactivo');

        $(".tab").hide();
        
        ///let actual_tab = $(this).data("tab"); 
        let tab = '#'+$(this).data("tab"),
            tabActive = $(this).data("tab");

        if (tabActive == "tab1") {
            resumen(idprod);
        }

        $(tab).fadeIn();
    });

    $(".report_process").on('click', function(e) {
        e.preventDefault();

        // TODO: poner las tablas en la ventana principal

        let tableContent = "",
            formData = new FormData(),
            row = "";

        formData.append('cc',$("#costosSearch").val());
        formData.append('id',idprod);

        if ($(this).data("categoria") == "pedidos"){
            //pedidos
            fetch(RUTA+'stocks/pedidos',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{

                    let estado = e.cant_aprob > 0 ? '' : 'semaforoRojo';
                    row = `<tr>
                        <td class="textoCentro">${e.pedido}</td>
                        <td class="textoDerecha">${e.cant_pedida}</td>
                        <td class="textoDerecha ${estado}">${e.cant_aprob}</td>
                        <td class="pl20px">${e.elabora}</td>
                        <td class="pl20px">${e.aprueba}</td>
                        <td class="pl20px">${e.cdesarea}</td>
                        <td class="textoCentro">${e.emision}</td>
                    </tr>`

                    $("#tbl_pedidos tbody").append(row);
                });            
                
                $("#tbl_pedidos").show();
            })
        }else if($(this).data("categoria") == "ordenes"){
            //ordenes
            fetch(RUTA+'stocks/ordenes',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{
                    row = `<tr>
                        <td class="textoCentro">${e.numero}</td>
                        <td class="textoDerecha">${e.emision}</td>
                        <td class="textoDerecha">${e.ncanti}</td>
                        <td class="pl20px">${e.cdesarea}</td>
                    </tr>`

                    $("#tbl_ordenes tbody").append(row);
                });            
                
                $("#tbl_ordenes").show();
            })
        }else if($(this).data("categoria") == "ingresos"){
            //recepcion almacenes
            fetch(RUTA+'stocks/ingresos',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{
                    row = `<tr>
                        <td class="textoCentro">${e.nnronota}</td>
                        <td class="textoCentro">${e.cnumguia}</td>
                        <td class="pl20px">${e.crazonsoc}</td>
                        <td class="textoDerecha">${e.ncantidad}</td>
                        <td class="textoCentro">${e.emision}</td>
                    </tr>`

                    $("#tbl_ingresos tbody").append(row);
                });            
                
                $("#tbl_ingresos").show();
            })
        }else if($(this).data("categoria") == "despachos"){
            fetch(RUTA+'stocks/despachos',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{

                    let guia = e.cnumguia == null ? "": e.cnumguia,
                        guiasunat = e.guiasunat == null ? "" : e.guiasunat;

                    row = `<tr data-iddespacho="${e.niddeta}">
                        <td class="textoCentro">${e.nnronota}</td>
                        <td class="textoCentro">${e.ffecdoc}</td>
                        <td class="pl20px">${e.ffecenvio}</td>
                        <td class="textoCentro">${guia}</td>
                        <td class="textoCentro">${guiasunat}</td>
                        <td class="textoDerecha">${e.ndespacho}</td>
                    </tr>`

                    $("#tbl_despachos tbody").append(row);
                });            
                
                $("#tbl_despachos").show();
            })
        }else if($(this).data("categoria") == "registros"){
            fetch(RUTA+'stocks/ingresoAlmacen',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{

                    let nombres = e.cnombres == null ? "" : e.cnombres,
                        guiasunat = e.guiasunat == null ? "" : e.guiasunat;

                    row = `<tr>
                        <td class="textoCentro">${e.idreg}</td>
                        <td class="textoCentro">${e.ffechadoc}</td>
                        <td class="pl20px">${e.numguia}</td>
                        <td class="pl20px">${nombres}</td>
                        <td class="textoDerecha">${e.cant_ingr}</td>
                        <td class="textoCentro">${guiasunat}</td>
                    </tr>`

                    $("#tbl_almacen tbody").append(row);
                });            
                
                $("#tbl_almacen").show();
            })
        }else if($(this).data("categoria") == "consumos"){
            fetch(RUTA+'stocks/consumos',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{
                    row = `<tr>
                        <td class="textoCentro">${e.nkardex}</td>
                        <td class="textoCentro">${e.fechasalida}</td>
                        <td class="pl20px">${e.cantsalida}</td>
                        <td class="pl20px">${e.cnombres}</td>
                    </tr>`

                    $("#tbl_consumos tbody").append(row);
                });            
                
                $("#tbl_consumos").show();
            })
        }else if($(this).data("categoria") == "devoluciones"){
            fetch(RUTA+'stocks/devoluciones',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{
                    row = `<tr>
                        <td class="textoCentro">${e.nkardex}</td>
                        <td class="textoCentro">${e.fechadevolucion}</td>
                        <td class="pl20px">${e.cantdevolucion}</td>
                        <td class="pl20px">${e.cnombres}</td>
                    </tr>`

                    $("#tbl_devolucion tbody").append(row);
                });            
                
                $("#tbl_devolucion").show();
            })
        }else if($(this).data("categoria") == "inventarios"){
            fetch(RUTA+'stocks/inventarios',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{
                    row = `<tr>
                        <td class="textoCentro">${e.idreg}</td>
                        <td class="textoCentro">${e.ffechadoc}</td>
                        <td class="pl20px">${e.cant_ingr}</td>
                        <td class="pl20px">${e.cnombres}</td>
                    </tr>`

                    $("#tbl_inventario tbody").append(row);
                });            
                
                $("#tbl_inventario").show();
            })
        }else if($(this).data("categoria") == "transferencias"){
            fetch(RUTA+'stocks/transferencias',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                data.registros.forEach((e)=>{
                    let direccion = e.idcd = $("#costosSearch").val() ? '<i class="fas fa-long-arrow-alt-left"></i>':'<i class="fas fa-long-arrow-alt-right"></i>';
                        guia = e.cnumguia == null ? "" : e.cnumguia;

                    row = `<tr>
                        <td class="textoCentro">${e.idtransfer}</td>
                        <td class="textoCentro">${guia}</td>
                        <td class="textoCentro">${e.costoOrigen}</td>
                        <td class="textoCentro">${e.costoDestino}</td>
                        <td class="textoDerecha">${e.ncanti}</td>
                        <td class="textoDerecha">${direccion}</td>
                    </tr>`

                    $("#tbl_transferencias tbody").append(row);
                });            
                
                $("#tbl_transferencias").show();
            })
        }
        
        $("#vistraTrazable").fadeIn();

        return false;
    });

    $("#closeTrazable").click(function(e){
        e.preventDefault();

        $("#vistraTrazable").fadeOut(function(){
            $(".tabla tbody").empty();
            $(".tabla").hide();
        });
        
        return false;
    });
})

resumen = (codigo_producto) => {
     $.post(RUTA+"stocks/resumen",{codigo:codigo_producto,cc:$("#costosSearch").val()},
            function (data, textStatus, jqXHR) {
               //pedidos
                $("#tabla1_tab1 tbody").find('tr').eq(0).find('td').eq(1).text(esnulo(data.pedidos.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(0).find('td').eq(2).text(esnulo(data.pedidos.cantidad));

                //ordenes
                $("#tabla1_tab1 tbody").find('tr').eq(1).find('td').eq(1).text(esnulo(data.ordenes.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(1).find('td').eq(2).text(esnulo(data.ordenes.cantidad));

                //recepcion
                $("#tabla1_tab1 tbody").find('tr').eq(2).find('td').eq(1).text(esnulo(data.recepcion.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(2).find('td').eq(2).text(esnulo(data.recepcion.cantidad));

                //despacho
                $("#tabla1_tab1 tbody").find('tr').eq(3).find('td').eq(1).text(esnulo(data.despacho.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(3).find('td').eq(2).text(esnulo(data.despacho.cantidad));

                //ingresos Obra
                $("#tabla1_tab1 tbody").find('tr').eq(4).find('td').eq(1).text(esnulo(data.existencias.numeros));
                $("#tabla1_tab1 tbody").find('tr').eq(4).find('td').eq(2).text(esnulo(data.existencias.cantidad));

                //Consumos
                $("#tabla2_tab1 tbody").find('tr').eq(0).find('td').eq(1).text(esnulo(data.consumos.numeros));
                $("#tabla2_tab1 tbody").find('tr').eq(0).find('td').eq(2).text(esnulo(data.consumos.cantidad));

                //Devoluciones
                $("#tabla2_tab1 tbody").find('tr').eq(1).find('td').eq(1).text(esnulo(data.devoluciones.numeros));
                $("#tabla2_tab1 tbody").find('tr').eq(1).find('td').eq(2).text(esnulo(data.devoluciones.cantidad));
                
                //Inventarios
                $("#tabla2_tab1 tbody").find('tr').eq(2).find('td').eq(1).text(esnulo(data.inventarios.numeros));
                $("#tabla2_tab1 tbody").find('tr').eq(2).find('td').eq(2).text(esnulo(data.inventarios.cantidad));

                //Transferencias
                $("#tabla2_tab1 tbody").find('tr').eq(3).find('td').eq(1).text(esnulo(data.transferencias.numeros));
                $("#tabla2_tab1 tbody").find('tr').eq(3).find('td').eq(2).text(esnulo(data.transferencias.cantidad));

                let saldo = (parseFloat(esnulo(data.existencias.cantidad))+
                            parseFloat(esnulo(data.inventarios.cantidad))+
                            parseFloat(esnulo(data.devoluciones.cantidad))) - parseFloat(esnulo(data.consumos.cantidad));

                $("#tabla2_tab1 tbody").find('tr').eq(4).find('td').eq(1).text(saldo);

                $("#tabla1_tab2 tbody")
                    .empty()
                    .append(data.minimos);

                $("#tabla1_tab3 tbody")
                    .empty()
                    .append(data.precios);
                


                $("#vistadocumento").fadeIn();
            },
            "json"
        );
}

detalles = () =>{
    DATA = [];
    let TABLA = $("#tablaPrincipal tbody >tr");

    TABLA.each(function(){
        item= {};
        
        item['item']            = $(this).find('td').eq(0).text();
        item['codigo']          = $(this).find('td').eq(1).text();
        item['descripcion']     = $(this).find('td').eq(2).text();
        item['unidad']          = $(this).find('td').eq(3).text();
        item['ingreso']         = $(this).find('td').eq(4).text();
        item['inventario']      = $(this).find('td').eq(5).text();
        item['salida']          = $(this).find('td').eq(6).text();
        item['devuelto']        = $(this).find('td').eq(7).text();
        item['transferencias']  = $(this).find('td').eq(8).text();
        item['ajustes']         = $(this).find('td').eq(9).text();
        item['minimos']         = $(this).find('td').eq(10).text();
        item['saldo']           = $(this).find('td').eq(11).text();
        item['grupo']           = $(this).data("grupo");
        item['clase']           = $(this).data("clase");
        item['familia']         = $(this).data("familia");

        item['a1']           = $(this).find('td').eq(12).text();
        item['a2']           = $(this).find('td').eq(13).text();
        item['b1']           = $(this).find('td').eq(14).text();
        item['b2']           = $(this).find('td').eq(15).text();
        item['a3']           = $(this).find('td').eq(16).text();
        item['b3']           = $(this).find('td').eq(17).text();
        item['c3']           = $(this).find('td').eq(18).text();
            
        DATA.push(item);
    })

    return DATA;
}

esnulo = (valor) => {
    return v = valor === null ? '0.00' : valor;
}

progreso = () => {
    $.post(RUTA+"stocks/conteo",
        function (data, textStatus, jqXHR) {
            //console.log(data);
        },
        "text"
    );
}

vueltas = (str) => {
    $.post(RUTA+"stocks/vueltas",str,
        function (data, textStatus, jqXHR) {
            //console.log(data);
        },
        "text"
    );
}
