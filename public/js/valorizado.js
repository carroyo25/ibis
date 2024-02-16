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
            formData.append("numero","");

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
                                <a href="${element.id_regmov}" data-id="${element.id_regmov}" title="${element.cObservacion}">
                                    <i class="fas fa-folder" style="color: #FFD43B;"></i>
                                    <p>${tipo}: ${element.cnumero}</p>
                                </a>
                            </li>`;
                    
                    $(".listaCarpetas ul").empty().append(row);    
                });

                $("#esperar").css("opacity","0").fadeOut();
                $("#vistaCarpetas").fadeIn();
            })
            
        } catch (error) {
            mostrarMensaje(error,'mensaje_error');  
        }

        return false;
    });

    $(".listaCarpetas ul").on("click","a", function (e) {
        e.preventDefault();
        
        let formData = new FormData(),
            row = "";
        formData.append("orden",$(this).attr('href'))

        fetch(RUTA+"valorizado/adjuntosArchivos",{
            method:"POST",
            body:formData
        })
        .then(response =>response.json())
        .then(data => {
            let archivos =  data.ordenes;

            if (data.guiasalmacen.length > 0) {
                archivos = data.ordenes.concat(data.guiasalmacen);
            }

            //public/documentos/ordenes/adjuntos/

            archivos.forEach(element => {
                let ext = getFileExtension3(element.creferencia),
                    fileIcon = '<i class="far fa-file"></i>',
                    ruta = "";

                switch (ext) {
                    case 'pdf':
                        fileIcon = '<i class="fas fa-file-pdf" style="color: #dd5836;"></i>';
                        break;
                    case 'msg':
                        fileIcon = '<i class="fas fa-envelope-open-text" style="color: #63E6BE;"></i>'; 
                        break;
                    case 'xls':
                        fileIcon = '<i class="fas fa-file-excel" style="color: #04b983;"></i>'; 
                        break;
                    case 'xlsx':
                        fileIcon = '<i class="fas fa-file-excel" style="color: #04b983;"></i>'; 
                        break;
                    case 'doc':
                        fileIcon = '<i class="fas fa-file-word" style="color: #2b72ee;"></i>';
                        break;
                    case 'docx':
                        fileIcon = '<i class="fas fa-file-word" style="color: #2b72ee;"></i>'; 
                        break;
                    case 'rar':
                        fileIcon = '<i class="fas fa-file-archive" style="color: #f051c6;"></i>'; 
                        break;
                    case 'zip':
                        fileIcon = '<i class="fas fa-file-archive" style="color: #f051c6;"></i>'; 
                        break;
                    case 'xls':
                        fileIcon = '<i class="fas fa-file-excel" style="color: #04b983;"></i>'; 
                        break;
                    case 'jpg':
                        fileIcon = '<i class="far fa-images" style="color: #acb1b9;"></i>'; 
                        break;
                    case 'jpeg':
                        fileIcon = '<i class="far fa-images" style="color: #acb1b9;"></i>';
                        break;
                    case 'png':
                        fileIcon = '<i class="far fa-images" style="color: #acb1b9;"></i>'; 
                        break;
                    case 'gif':
                        fileIcon = '<i class="far fa-images" style="color: #acb1b9;"></i>'; 
                        break;
                }

                if (element.cmodulo == "ORD") {
                    ruta = "http://sicalsepcon.net/ibis/public/documentos/ordenes/adjuntos/"+element.creferencia;
                }else {
                    ruta = "http://sicalsepcon.net/ibis/public/documentos/almacen/adjuntos/"+element.creferencia;
                }
                
                row +=` <li>
                            <a href="${ruta}" title="${element.mensaje}">
                                ${fileIcon}
                                <p>${element.documento}</p>
                            </a>
                        </li>`;

                $("#listaAdjuntos").empty().append(row);  
            })

            $("#vistaAdjuntos").fadeIn();
        })

        return false;
    });

    $("#listaAdjuntos").on("click","a", function(e) {
        e.preventDefault();

        $(".ventanaAdjuntos iframe").attr("src", $(this).attr("href"));

        return false;
    });
    
    $("#closeAtach").click(function (e) { 
        e.preventDefault();

        $(".ventanaAdjuntos iframe").attr("src","");
        $("#vistaCarpetas").fadeOut();

        return false;
    });

    $("#closeAtachFiles").click(function (e) { 
        e.preventDefault();

        $(".ventanaAdjuntos iframe").attr("src","");
        $("#vistaAdjuntos").fadeOut();

        return false;
    });

    $("#ordenSearch").keypress(function (e) { 
        if(e.which == 13) { 
            let row = "",
            formData = new FormData();
                formData.append("cc",$("#costosSearch").val());
                formData.append("anio",$("#anioSearch").val());
                formData.append("numero",$(this).val());

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
                                <a href="${element.id_regmov}" data-id="${element.id_regmov}" title="${element.cObservacion}">
                                    <i class="fas fa-folder" style="color: #FFD43B;"></i>
                                    <p>${tipo}: ${element.cnumero}</p>
                                </a>
                            </li>`;
                    
                    $(".listaCarpetas ul").empty().append(row);    
                });

                $("#esperar").css("opacity","0").fadeOut();
                $("#vistaCarpetas").fadeIn();
            })
        }
    });
})

function getFileExtension3(filename) {
    return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
}