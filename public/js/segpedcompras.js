$(function(){
        let pedido = "",
            estadoTexto = "";

        //$("#esperar").css({"display":"block","opacity":"1"});

        cargaPrincipal($("#numeroSearch").val(),$("#costosSearch").val(),$("#mesSearch").val(),$("#anioSearch").val());

        $("#tablaPrincipal tbody").on("click","a", function (e) {
            e.preventDefault();

            pedido = $(this).attr("href");

            $("#cambioestado").fadeIn();

            return false;
        });

        $("#tablaPrincipal tbody").on("click","tr", function (e) {
            e.preventDefault();

            $.post(RUTA+"segpedcompras/consultaId", {id:$(this).data("indice")},
                    function (data, textStatus, jqXHR) {
                        
                        let numero = $.strPad(data.cabecera[0].nrodoc,6);
                        let estado = "textoCentro w50por estado " + data.cabecera[0].cabrevia;
                        
                        $("#codigo_costos").val(data.cabecera[0].idcostos);
                        $("#codigo_area").val(data.cabecera[0].idarea);
                        $("#codigo_transporte").val(data.cabecera[0].idtrans);
                        $("#codigo_solicitante").val(data.cabecera[0].idsolicita);
                        $("#codigo_tipo").val(data.cabecera[0].idtipomov);
                        $("#codigo_pedido").val(data.cabecera[0].idreg);
                        $("#codigo_estado").val(data.cabecera[0].estadodoc);
                        $("#codigo_verificacion").val(data.cabecera[0].verificacion);
                        $("#codigo_atencion").val(data.cabecera[0].nivelAten);
                        $("#emitido").val(data.cabecera[0].docPdfEmit);
                        $("#elabora").val(data.cabecera[0].cnombres);
                        $("#numero").val(numero);
                        $("#emision").val(data.cabecera[0].emision);
                        $("#costos").val(data.cabecera[0].proyecto);
                        $("#area").val(data.cabecera[0].area);
                        $("#transporte").val(data.cabecera[0].transporte);
                        $("#concepto").val(data.cabecera[0].concepto);
                        $("#solicitante").val(data.cabecera[0].nombres);
                        $("#tipo").val(data.cabecera[0].tipo);
                        $("#vence").val(data.cabecera[0].vence);
                        $("#estado").val(data.cabecera[0].estado);
                        $("#espec_items").val(data.cabecera[0].detalle);
                        $("#user_asigna").val(data.cabecera[0].asigna);
                        
                        $("#tablaDetalles tbody")
                            .empty()
                            .append(data.detalles);

                        $("#estado")
                            .removeClass()
                            .addClass(estado);
                    },
                    "json"
                );

            $("#proceso").fadeIn();

            return false;
        });

        $("#closeProcess").click(function (e) { 
            e.preventDefault();

            $("#proceso").fadeOut();
            
            return false;  
        });

        $("#operadores").on("click","a", function (e) {
            e.preventDefault();
    
            $("#operadores *").removeClass("itemSeleccionado");
            $(this).addClass("itemSeleccionado");
            $("#estadoCompra").val($(this).attr("href"));

            estadoTexto = $(this).text();
            
    
            return false;
        });

        $("#cancelaEstado").click(function (e) { 
            e.preventDefault();
    
            $("#cambioestado").fadeOut();
            
            return false;
        });
    
        $("#aceptaEstado").click(function (e) { 
            e.preventDefault();
    
            try {
                let formData = new FormData();
                    formData.append("id",pedido);
                    formData.append("estado",$("#estadoCompra").val());
                    formData.append("comentario",$("#comentarioEstado").val());
                    formData.append("user",$("#id_user").val());
                    formData.append("fechaObra",$("#entregaObra").val());

                if ($("#estadoCompra").val() =="" ) throw "Seleccione el estado para asignar al pedido";

                fetch(RUTA+"segpedcompras/estadocompra",{
                    method: "POST",
                    body:formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.respuesta){
                        mostrarMensaje(data.mensaje,"mensaje_correcto");

                        let entrega = $("#entregaObra").val().split("-");

                        $('#'+pedido+' td:first').parent().find('td').eq('8').text(entrega[2]+'/'+entrega[1]+'/'+entrega[0]);
                        $('#'+pedido+' td:first').parent().find('td').eq('9').children().text(estadoTexto).attr("data-title",$("#comentarioEstado").val());

                    }else{
                        mostrarMensaje(data.mensaje,"mensaje_error");
                    }

                    $("#cambioestado").fadeOut();
                    
                })
            } catch (error) {
                mostrarMensaje(error,"mensaje_error")
            }
            
            return false;
        });

        $("#btnConsulta").click((e) => {
            e.preventDefault();

            cargaPrincipal($("#numeroSearch").val(),$("#costosSearch").val(),$("#mesSearch").val(),$("#anioSearch").val());

            return false;
        })

        $("#reportExport").click((e) => {
            e.preventDefault();

            $("#esperar").css({"display":"block","opacity":"1"});

            crearReporte(getDataTable());

            $("#esperar").css({"display":"none","opacity":"0"});

            return false;
        })
    })

    function cargaPrincipal(pedido,costos,mes,anio) {
        formData = new FormData();
        formData.append("pedido",pedido);
        formData.append("costos",costos);
        formData.append("mes",mes);
        formData.append("anio",anio);

        $("#esperar").css({"display":"block","opacity":"1"});

        fetch(RUTA+'segpedcompras/consultarPedidos',{
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            $("#tablaPrincipal tbody").empty();

            data.datos.forEach(element =>{
                let tipo = element.idtipomov == 37 ? "B":"S",
                    asignado = element.cnameuser == null ? "--" : element.cnameuser,
                    comentario = element.comentariocompra == null ? "--": element.comentariocompra,
                    entrega = element.entrega == null ? "" : element.entrega;


                let row = `<tr class="pointer" data-indice="${element.idreg}" data-compras="${element.estadoCompra}" id="${element.idreg}">
                                <td class="textoCentro">${element.nrodoc}</td>
                                <td class="textoCentro">${element.emision}</td>
                                <td class="textoCentro">${tipo}</td>
                                <td class="pl20px">${element.concepto}</td>
                                <td class="pl20px">${element.costos}</td>
                                <td class="pl20px">${element.nombres}</td>
                                <td class="textoCentro ${element.cabrevia}">${element.estado}</td>
                                <td class="textoCentro">${asignado}</td>
                                <td class="textoCentro">${entrega}</td>
                                <td class="textoCentro" style="font-size:.6rem">
                                    <a href="${element.idreg}" data-title="${comentario}" class="bocadillo">${element.textoEstadoCompra}</a>
                                </td>
                                <td>${element.itemsFaltantes}/${element.itemsConOrden}</td>
                                <td class="textoCentro">
                                    <a href="${element.idreg}">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                </td>
                            </tr>`;
                
                if (element.itemsFaltantes > 0) {
                    $("#tablaPrincipal tbody").append(row);
                }
            })

            $("#esperar").fadeOut().promise().done(function(){
                iniciarPaginador();
            });

            $("#esperar").css({"display":"none","opacity":"0"});
        })
    }

    async function crearReporte(filas){
        const workbook = new ExcelJS.Workbook();
        workbook.creator = 'Sical';
        workbook.lastModifiedBy = 'Sical';
        workbook.created = new Date();
        workbook.modified = new Date();
    
        const worksheet = workbook.addWorksheet('Estado de Pedidos - Compras');
        const columns = [{ width: 10 },
                         { width: 15 },
                         { width: 8 },
                         { width: 80 },
                         { width: 85 },
                         { width: 50 },
                         { width: 20 },
                         { width: 20 },
                         { width: 60 },
                         { width: 20 },
                         { width: 60 }
        ];

        worksheet.mergeCells('A1:K1');
        worksheet.getCell('A1').value = 'Seguimiento de Pedidos Compras';
       
        worksheet.getRow(2).height = 30;

        worksheet.columns = columns;

        const headers = ['Nro','Emision','Tipo','Descripcion','Proyecto','Usuario','Estado','Asignado','Estado Compras','Entrega Obra','Comentarios'];

        /*rellenar los datos*/
         worksheet.getRow(2).values=headers;
         
         filas.forEach((fila,index) => {
            worksheet.addRow([
                fila.numero,
                fila.emision,
                fila.tipo,
                fila.descripcion,
                fila.proyecto,
                fila.solicitante,
                fila.estado,
                fila.asignado,
                fila.compras,
                fila.entrega,
                fila.comentarios
            ])
         })
         // Configurar wrapText para cada columna
         headers.forEach((header, index) => {
             const columnIndex = index + 1; // Las columnas en ExcelJS comienzan en 1
             worksheet.getColumn(columnIndex).alignment = { wrapText: true };  // Aplicar wrapText a toda la columna
         });
        
        worksheet.getCell('A1').alignment = { horizontal: 'center', vertical: 'center' };

        applyBackgroundColor(worksheet, 2, 2, 1, 11, 'BFCDDB');

        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

        // Descargar archivo
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'reporteprocura.xlsx';
        a.click();
        URL.revokeObjectURL(url);

        $("#esperar").css({"display":"none","opacity":"0"});
    }

    getDataTable = () =>{
        DATA = [];

        let TABLA = $("#tablaPrincipal tbody >tr");

        TABLA.each(function(){
            item= {};
            
            item['numero']      = $(this).find('td').eq(0).text();
            item['emision']     = $(this).find('td').eq(1).text();
            item['tipo']        = $(this).find('td').eq(2).text();
            item['descripcion'] = $(this).find('td').eq(3).text();
            item['proyecto']    = $(this).find('td').eq(4).text();
            item['solicitante'] = $(this).find('td').eq(5).text();
            item['estado']      = $(this).find('td').eq(6).text();
            item['asignado']    = $(this).find('td').eq(7).text();
            item['entrega']     = $(this).find('td').eq(8).text();
            item['compras']     = $(this).find('td').eq(9).children().text();
            item['comentarios'] = $(this).find('td').eq(9).children().data('title');
            

            DATA.push(item);
        })
        return DATA;
    }

    function applyBackgroundColor(worksheet, startRow, endRow, startCol, endCol, color) {
        for (let row = startRow; row <= endRow; row++) {
            for (let col = startCol; col <= endCol; col++) {
                // Convertir el índice de columna numérico a su letra correspondiente (por ejemplo, 1 => 'A', 2 => 'B', etc.)
                const cellRef = worksheet.getColumn(col).letter + row;
                console.log(cellRef)
                worksheet.getCell(cellRef).style = {
                    fill : {
                    type: 'pattern',  // Tipo de relleno
                    pattern: 'solid', // Tipo sólido
                    fgColor: { argb: color },  // Color de fondo en formato ARGB
                    bgColor: { argb: color }
                },
                alignment : {
                    horizontal: 'center',
                    vertical: 'middle',
                    wrapText: true
                }
            }}
        }
    }

    
    