$(function (){
    var index = 0;
    var errorFile = false;
    var igv = 0;
    
    $("#btnSend").click(function (e) { 
        e.preventDefault();
        
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

        let result = [];

        $.each($("#proforma").serializeArray(),function(){
            result[this.name] = this.value;
        });

        try {
            if (result['fecha_vence'] == "") throw "indique la fecha de vencimiento";
            if (result['nro_cot'] == "") throw "indique el nro de cotizacion";
            if (result['moneda'] == -1) throw "Elige el tipo de moneda";
            if (result['cond_pago'] == -1) throw "Elige el tipo de pago";
            if ( $('input:radio[name=radioIgv]:checked').val() == undefined ) throw "Indique si incluye IGV";
            if (checkCantTables($(".tablas_items tbody > tr"),9)) throw "No indico la fecha de entrega";
            if (errorFile) throw "El archivo de la cotizacion no es formato correcto(PDF)";

            $("#proforma").trigger("submit");
            
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');
        }
        
        return false;
    });

    $("#proforma").submit(function (e) { 
        e.preventDefault();
        
        let aInfo = new FormData( this );
            aInfo.append("funcion","enviarProforma");
            aInfo.append("ruc",$("#ruc").text());
            aInfo.append("detalles",JSON.stringify(getItems()));

        $.ajax({
            url:'acciones.php',
            // Request type
            type: "POST", 
            // To send the full form data
            data: aInfo,
            contentType:false,      
            processData:false,
            dataType:"json",
            success: function (response) {
                if(response.respuesta){
                    mostrarMensaje(response.mensaje,"mensaje_correcto");
                    $("#pregunta").fadeOut();
                    $(".opciones").remove();
                }else{
                    mostrarMensaje(response.mensaje,"mensaje_error");
                };
            }
        });

        return false;
    });

    $("#btnAtach").click(function (e) { 
        e.preventDefault();
        
        $("#cotizacion").trigger("click");

        return false;
    });

    $(".tablas_items tbody").on("keypress",".precio", function (e) {
        if (e.which == 13){
            try {
                if ( $('input:radio[name=radioIgv]:checked').val() == undefined) throw "El precio incluye IGV?";
                
                let cant = $(this).parent().parent().find("td").eq(4).text();
                let precio = $(this).val();
                let total = (parseFloat(cant) * parseFloat(precio));
                let suma = 0;

                igv = parseFloat($('input:radio[name=radioIgv]:checked').val());

                $(this).parent().parent().find("td").eq(6).text(parseFloat(total.toFixed(2)));
                
                $('.tablas_items tbody > tr').each(function(){ //filas con clase 'dato', especifica una clase, asi no tomas el nombre de las columnas
                    suma += parseFloat($(this).find('td').eq(6).text()||0,10) //numero de la celda 3
                })

                if ( suma > 0 ) {
                    $("#stotal").val(numberWithCommas(suma.toFixed(2)));
                    $("#st").val(suma.toFixed(2));
                    $("#igv").val(numberWithCommas((suma*igv).toFixed(2)));
                    $("#si").val((suma*igv).toFixed(2));
                    $("#total").val(numberWithCommas((suma*(1+igv)).toFixed(2)));
                    $("#to").val((suma*(1+igv)).toFixed(2));
                    $("#total_numero").val((suma*(1+igv)).toFixed(2));
                }
            } catch (error) {
                mostrarMensaje(error,'mensaje_error');
            }
        }
    });

    $(".tablas_items tbody").on("click","a", function (e) {
        e.preventDefault();

        $("#itemAtach").trigger("click");

        index = $(this).data("row");

        return false;
    });

    $("#itemAtach").change(function (e) { 
        e.preventDefault();
        
        let dato_archivo = $(this).prop("files")[0];
        let form_data = new FormData();

        form_data.append("file",dato_archivo);
        form_data.append("funcion","subirAdjunto");

        $.ajax({
            url:'acciones.php',
            // Request type
            type: "POST", 
            // To send the full form data
            data: form_data,
            contentType:false,      
            processData:false,
            dataType:"text",
            success: function (response) {
                $(".tablas_items tbody").find('tr').eq(index).attr('data-adjunto',response);
            }
        });

        return false;
       

    });

    $("#cotizacion").change(function (e) { 
        e.preventDefault();
        
        if (this.files[0].name.match(/\.(jpg|jpeg|png|gif)$/)) {
            errorFile = true;
        }

        return false;
    });
})

const getItems  = () => {
    DETALLES = [];

    var TABLA = $(".tablas_items tbody > tr");

    TABLA.each(function(){
        let CODPROD  = $(this).data('codprod'),
            IDPEDDET = $(this).data('iddetped'),
            ADJUNTO  = $(this).data('adjunto'),
            UNIDAD   = $(this).data('und'),
            CANTIDAD = $(this).find('td').eq(4).text(),
            PRECIO   = $(this).find('td').eq(5).children().val(),
            TOTAL    = $(this).find('td').eq(6).text(),
            IGV      = 0,
            OBSERVAC = $(this).find('td').eq(8).children().val(),
            ENTREGA  = $(this).find('td').eq(9).children().val(),

        item ={}

        item['codprod']     = CODPROD;
        item['idpedet']     = IDPEDDET;
        item['adjunto']     = ADJUNTO;
        item['unidad']      = UNIDAD;
        item['cantidad']    = CANTIDAD;
        item['precio']      = PRECIO;
        item['total']       = TOTAL;
        item['observa']     = OBSERVAC;
        item['entrega']     = ENTREGA;
        item['igv']         = IGV;

        DETALLES.push(item);
    })

    return DETALLES;
}