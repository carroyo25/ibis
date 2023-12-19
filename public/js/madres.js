$(() =>{
    const body = document.querySelector("#tablaPrincipal tbody");

    let listItemFinal = null,estoyPidiendo = false,accion = "";

    //animateprogress("#php",72);

    //LISTA PARA EL SCROLL

    const observandoListItem = listItem => {
        if ( listItem[0].isIntersecting ) {
            query();
        }
    }

    const settings = {
        threshold: 1
    }

    let observador = new IntersectionObserver(
        observandoListItem,
        settings
    );

    const query = async () => {
        if (estoyPidiendo) return;
        estoyPidiendo = true;
        let pagina = parseInt(body.dataset.p) || 1;
        const FD = new FormData();
        FD.append('pagina',pagina);

        const r = await fetch(RUTA+'madres/listaScroll',{
            method: 'POST',
            body:FD
        });

        let estado = "No enviado",indicador = "urgente";

        const j  = await r.json();
        j[0].guias.forEach(i => {
            const tr = document.createElement('tr');

            if (i.nflgSunat == 1) {
                estado = "Enviado";
                indicador = "normal";
            }
            
            tr.innerHTML = `<td class="textoCentro">${i.cnroguia}</td>
                            <td class="textoCentro">${i.emision}</td>
                            <td class="textoCentro">${i.traslado}</td>
                            <td class="textoCentro">${i.almacen_origen}</td>
                            <td class="pl20px">${i.almacen_destino}</td>
                            <td class="textoCentro ${indicador}">${estado}</td>`;
            tr.classList.add("pointer");
            tr.dataset.indice = i.idreg;
            body.appendChild(tr);
        })

        if (listItemFinal){
            observador.unobserve(listItemFinal);
        }

        if (j[0].quedan) { //devuelve falso si ya no quedan mas registros
            listItemFinal = body.lastElementChild.previousElementSibling;
            observador.observe( listItemFinal);
            estoyPidiendo = false;
            body.dataset.p = ++pagina;
        }
    }

    query();

    ///FIN DEL SCROLL


    $("#esperar").fadeOut();

    $("#nuevoRegistro").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeIn();

        accion = 'n';

        document.getElementById("formProceso").reset();
        document.getElementById("guiaremision").reset();
        $("#tablaDetalles tbody").empty();

        return false;
    });

    $("#closeProcess").click(function (e) { 
        e.preventDefault();

        $("#proceso").fadeOut();

        return false;
    });

    $(".mostrarLista").focus(function (e) { 
        e.preventDefault();

        if (accion !="n") {
            return false;
        }
        
        $(this).next().slideDown();

        return false;
    });

    $(".cerrarLista").focus(function (e) { 
        e.preventDefault();
        
        $(".lista").fadeOut();

        return false;
    });

    $(".lista").on("click",'a', function (e) {
        e.preventDefault();

        let control = $(this).parent().parent().parent();
        let destino = $(this).parent().parent().parent().prev();
        let contenedor_padre = $(this).parent().parent().parent().attr("id");
        let id = "";
        let codigo = $(this).attr("href");
        let almacen = $(this).data("almacen");
        
        control.slideUp()
        destino.val($(this).text());
        id = destino.attr("id");

        if(contenedor_padre == "listaCostosDestino"){
            $("#codigo_costos_origen").val(codigo);
            //$("#codigo_almacen_destino").val(almacen);
        }else if(contenedor_padre == "listaAprueba"){
            $("#codigo_aprueba").val(codigo);
        }else if(contenedor_padre == "listaOrigen"){
            $("#codigo_almacen_origen").val(codigo);
            $("#codigo_origen").val(codigo);
            $("#almacen_origen").val($(this).text());
            $("#almacen_origen_direccion").val($(this).data('direccion'));
        }else if(contenedor_padre == "listaDestino"){
            $("#codigo_almacen_destino").val(codigo);
            $("#almacen_destino").val($(this).text());
            $("#almacen_destino_direccion").val($(this).data('direccion'));
        }else if(contenedor_padre == "listaModalidad"){
            $("#modalidad_traslado").val($(this).text());
            $("#codigo_modalidad").val(codigo);
        }else if(contenedor_padre == "listaEnvio"){
            $("#tipo_envio").val($(this).text());
            $("#codigo_tipo").val(codigo);
        }else if(contenedor_padre == "listaEntidad"){
            $("#codigo_entidad_transporte").val(codigo);
            $("#empresa_transporte_razon").val($(this).text());
            $("#ruc_proveedor").val($(this).data("ruc"));
            $("#direccion_proveedor").val($(this).data("direccion"));
        }else if(contenedor_padre == "listaAutoriza"){
            $("#autoriza").val($(this).text());
            $("#codigo_autoriza").val(codigo);
        }else if(contenedor_padre == "listaDespacha"){
            $("#codigo_despacha").val(codigo);
        }else if(contenedor_padre == "listaDestinatario"){
            $("#destinatario").val($(this).text());
            $("#codigo_destinatario").val(codigo);
        }else if(contenedor_padre == "listaMovimiento"){
            $("#codigo_movimiento").val(codigo);
            $("#tipo_envio").val($(this).text());
        }

        return false;
    });

    $("#importData").click(function (e) { 
        e.preventDefault();

        try {
            if ($("#codigo_aprueba").val() == 0) throw "Elija la persona que aprueba";
            if ($("#codigo_costos_origen").val() == 0) throw "Indique el centro de costos"; 

            $("#esperar").fadeIn();

            $.post(RUTA+"madres/guias", {cc:$("#codigo_costos_origen").val(),guia:""},
                function (data, textStatus, jqXHR) {
                    $("#tablaGuias tbody")
                        .empty()
                        .append(data);

                        $("#guias").fadeIn();
                        $("#esperar").fadeOut();
                },
                "text"
            );
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
        
        return false
    });

    $(".tituloVentana").on("click","a", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().fadeOut();

        return false;
    });

    $("#txtBuscarGuia").keyup(function (e) { 
        if(e.which == 13) {
            $("#esperar").fadeIn();
            
            try {
                if ($("#codigo_aprueba").val() == 0 ) throw "Elija la persona que aprueba";
                //if ($("#codigo_costos_destino").val() == 0 ) throw "Indique el centro de costos"; 
    
                $("#esperar").fadeIn();
    
                $.post(RUTA+"madres/guias", {cc:$("#codigo_costos_destino").val(),guia:$(this).val()},
                    function (data, textStatus, jqXHR) {
                        $("#tablaGuias tbody")
                            .empty()
                            .append(data);
    
                            $("#guias").fadeIn();
                            $("#esperar").fadeOut();
                    },
                    "text"
                );
            } catch (error) {
                mostrarMensaje(error,"mensaje_error");
            }
        }
    });

    $("#tablaGuias tbody").on("click","tr", function (e) {
        e.preventDefault();
        
        $("#esperar").fadeIn();
        
        $(this).remove();


        $.post(RUTA+"madres/itemsDespacho",{idx:$(this).data("despacho")},
            function (data, textStatus, jqXHR) {
                $("#tablaDetalles tbody").append(data);

                $("#esperar").fadeOut();
            },
            "text"
        );

        return false;
    });

    $("#guiaRemision").click(function (e) { 
        e.preventDefault();
        
        try {
            $("#vistadocumento").fadeIn();
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
         
        return false;
    });

    $(".tituloDocumento").on("click","#closeDocument", function (e) {
        e.preventDefault();

        $(this).parent().parent().parent().parent().parent().fadeOut();

        return false;
    });

    $(".btnCallMenu").click(function (e) { 
        e.preventDefault();
        
        let callButtom = e.target.id;

        $(this).next().fadeToggle();

        return false
    });

    $(".buscaGuia").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        
        if ($(this).val() == "") {
            $(".datosEntidad").val("");
            $(".lista").fadeOut();
        }else {
            //asignar a una variable el contenido
            let l = "#"+ $(this).next().next().attr("id")+ " li a"

            $(l).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        }
    });

    $("#previewDocument").click(function (e) { 
        e.preventDefault();
        
        try {
            let result = {};

            $.each($("#guiaremision").serializeArray(),function(){
                result[this.name] = this.value;
            });

            if (result['numero_guia'] == "") throw "Ingrese el Nro. de Guia";
            if (result['codigo_entidad'] == "") throw "Seleccione la empresa de transportes";
            if (result['codigo_traslado'] == "") throw "Seleccione la modalidad de traslado";
            
            
            $.post(RUTA+"salida/vistaPreviaGuiaRemision", {cabecera:result,
                                                            detalles:JSON.stringify(detalles()),
                                                            proyecto: $("#corigen").val()},
                function (data, textStatus, jqXHR) {
                        
                       if (data.archivo !== ""){
                            $(".ventanaVistaPrevia iframe")
                            .attr("src","")
                            .attr("src",data.archivo);
        
                            $("#vistaprevia").fadeIn();
                       }
                    },
                    "json"
            );
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false
    });

    $("#closePreview").click(function (e) { 
        e.preventDefault();

        $(".ventanaVistaPrevia iframe").attr("src","");
        $("#vistaprevia").fadeOut();

        return false;
    });

    $("#saveDocument").click(function(e){
        e.preventDefault();

        $.post(RUTA+"madres/grabaGuiaMadre", {detalles:JSON.stringify(detalles()),
                                                proyecto: $("#corigen").val(),
                                                guia:$("#numero_guia").val(),
                                                costos:$("#codigo_costos_origen").val(),
                                                aprueba:$("#codigo_autoriza").val(),
                                                recibe:$("#codigo_destinatario").val(),
                                                alm_origen:$("#codigo_almacen_origen").val(),
                                                alm_destino:$("#codigo_almacen_destino").val(),
                                                modalidad:$("#codigo_modalidad").val(),
                                                tipo:$("#codigo_tipo").val(),
                                                envio:$("#codigo_movimiento").val(),
                                                transportista:$("#codigo_entidad_transporte").val(),
                                                conductor:$("#nombre_conductor").val(),
                                                licencia:$("#licencia_conducir").val(),
                                                dni:$("#conductor_dni").val(),
                                                marca:$("#marca").val(),
                                                placa:$("#placa").val(),
                                                peso:$("#peso").val(),
                                                bultos:$("#bultos").val(),
                                                emision:$("#fgemision").val(),
                                                traslado:$("#ftraslado").val(),
                                                useremit:$("#id_user").val(),
                                                observaciones:$("#observaciones").val(),
                                                conductor:$("#nombre_conductor").val()},

                function (data, textStatus, jqXHR) {
                    mostrarMensaje(data.mensaje,data.clase);
                    $("#numero, #numero_guia").val(data.guia);
                },
                "json"
            );

        return false;
    });

    $("#tablaPrincipal tbody").on("click","tr", function (e) {
        e.preventDefault();

        $.post(RUTA+"madres/guiasRemision", {id:$(this).data("indice")},
            function (data, text, requestXHR) {

                $("#fecha").val(data.cabecera[0].ffecdoc);
                $("#numero").val(data.cabecera[0].idreg);

                $("#aprueba").val(data.cabecera[0].autoriza);
                $("#almacen_origen_despacho").val(data.cabecera[0].origen);
                $("#almacen_destino_despacho").val(data.cabecera[0].destino);
                $("#tipo").val(data.cabecera[0].movimiento);

                $("#numero_guia").val(data.cabecera[0].cnroguia);
                $("#fgemision").val(data.cabecera[0].ffecdoc);
                $("#ftraslado").val(data.cabecera[0].ffectraslado);
                $("#almacen_origen").val(data.cabecera[0].origen);
                $("#almacen_origen_direccion").val(data.cabecera[0].origen_direccion);
                $("#almacen_destino").val(data.cabecera[0].destino);
                $("#almacen_destino_direccion").val(data.cabecera[0].destino_direccion);
                $("#empresa_transporte_razon").val(data.cabecera[0].nombre_proveedor);
                $("#direccion_proveedor").val(data.cabecera[0].direccion_proveedor);
                $("#ruc_proveedor").val(data.cabecera[0].ruc_proveedor);
                $("#modalidad_traslado").val(data.cabecera[0].movimiento);
                $("#tipo_envio").val(data.cabecera[0].tipo_envio);
                $("#autoriza").val(data.cabecera[0].autoriza);
                $("#destinatario").val(data.cabecera[0].recibe);
                $("#observaciones").val(data.cabecera[0].cobserva);
                $("#nombre_conductor").val(data.cabecera[0].cConductor);
                $("#licencia_conducir").val(data.cabecera[0].clincencia);
                $("#conductor_dni").val(data.cabecera[0].ndni);
                $("#marca").val(data.cabecera[0].cmarca);
                $("#placa").val(data.cabecera[0].cplaca);
                $("#peso").val(data.cabecera[0].npeso);
                $("#bultos").val(data.cabecera[0].nbultos);
                $("#observaciones").val(data.cabecera[0].cObserva);

                $("#ubig_origen").val(data.cabecera[0].ubigeo_origen);
                $("#ubig_destino").val(data.cabecera[0].ubigeo_destino);

                $("#cso").val(data.cabecera[0].codigo_sunat_origen);
                $("#csd").val(data.cabecera[0].codigo_sunat_destino);

                $("#codigo_modalidad").val(data.cabecera[0].ntipmov);
                $("#codigo_tipo").val(data.cabecera[0].nmottranp);

                $("#tablaDetalles tbody").empty().append(data.detalles);

                $("#proceso").fadeIn();
            },
            "json"
        );

        return false;
    });

    $("#guiaSunat").click(function(e){
        e.preventDefault();

        let datosGuia = {},datosFormulario = {};
    
        $.each($("#guiaremision").serializeArray(),function(){
            datosGuia[this.name] = this.value;
        })

        $.each($("#formProceso").serializeArray(),function(){
            datosFormulario[this.name] = this.value;
        })

        $.post(RUTA+"madres/envioSunat", {datosGuia:JSON.stringify(datosGuia),
                                          datosFormulario:JSON.stringify(datosFormulario),
                                          detalles:JSON.stringify(detalles())},
            function (data, text, requestXHR) {
                //console.log(data);
            },
            "json"
        );

        return false;
    });
})

detalles = () =>{
    DETALLES = [];

    let TABLA = $("#tablaDetalles tbody >tr");
    
    TABLA.each(function(){
        let ITEM        = $(this).find('td').eq(0).text(),
            IDDETORDEN  = "",
            IDDETPED    = "",
            IDPROD      = "",
            IDDESPACHO  = $(this).data('itemdespacho'),
            DESPACHO    = $(this).data('despacho'),
            PEDIDO      = "",
            ORDEN       = "",
            INGRESO     = "",
            ALMACEN     = "",
            CANTDESP    = $(this).find('td').eq(4).text(),
            OBSER       = "",
            CODIGO      = $(this).find('td').eq(1).text(),//codigo
            DESCRIPCION = $(this).find('td').eq(2).text(),//descripcion
            UNIDAD      = $(this).find('td').eq(3).text(),//unidad
            DESTINO     = $("#codigo_almacen_destino").val(),
            CANTIDAD    = $(this).find('td').eq(4).text();
    
        item = {};

        //if (CHECKED == flag) {
            item['item']         = ITEM;
            item['iddetorden']   = IDDETORDEN;
            item['iddetped']     = IDDETPED;
            item['idprod']       = IDPROD;
            item['pedido']       = ORDEN;
            item['orden']        = PEDIDO;
            item['ingreso']      = INGRESO;
            item['almacen']      = ALMACEN;
            item['cantidad']     = CANTIDAD;
            item['cantdesp']     = CANTDESP;
            item['obser']        = OBSER;
            item['iddespacho']   = IDDESPACHO;
            item['despacho']     = DESPACHO;

            item['codigo']       = CODIGO;
            item['descripcion']  = DESCRIPCION;
            item['unidad']       = UNIDAD;
            item['destino']      = DESTINO;

            
            DETALLES.push(item);
        //}
    })

    return DETALLES; 
}

function animateprogress (id, val){    
    var getRequestAnimationFrame = function () {  /* <------- Declaro getRequestAnimationFrame intentando obtener la máxima compatibilidad con todos los navegadores */
        return window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||  
        window.mozRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        function ( callback ){
            window.setTimeout(enroute, 1 / 60 * 1000);
        };
    };
     
    var fpAnimationFrame = getRequestAnimationFrame();   /* <--- Declaro una instancia de getRequestAnimationFrame para llamar a la función animación */
    var i = 0;
    var animacion = function () {
             
    if (i<=val)
        {
            document.querySelector(id).setAttribute("value",i);      /* <----  Incremento el valor de la barra de progreso */
            document.querySelector(id+"+ span").innerHTML = i+"%";     /* <---- Incremento el porcentaje y lo muestro en la etiqueta span */
            i++;
            fpAnimationFrame(animacion);          /* <------------------ Mientras que el contador no llega al porcentaje fijado la función vuelve a llamarse con fpAnimationFrame     */
        }
                                         
    }
 
    fpAnimationFrame(animacion);   /*  <---- Llamo la función animación por primera vez usando fpAnimationFrame para que se ejecute a 60fps  */
                 
}