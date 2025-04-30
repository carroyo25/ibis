$(function() {

    $("#esperar").fadeOut();

    $(".carpetas").on("click","a", function (e) {
        e.preventDefault();

        let formData = new FormData(),
            id = $(this).attr('href');

        formData.append('id',id);

        fetch (RUTA+'certificados/adjuntos',{
            method:'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data =>{
            data.adjuntos.forEach(element =>{
                let row = `<li><a href="${element.creferencia}" class="icono_archivo"><i class="fas fa-file"></i><p>${element.cdocumento}</p></a></li>`;

                $("#listaAdjuntos").empty().append(row);
            })

            $("#vistaAdjuntos").fadeIn();
        })

        return false;
    });


    $("#closeAtach").click(function(e){
        e.preventDefault();

        $("#vistaAdjuntos").fadeOut();
        $(".ventanaAdjuntos iframe").attr("src","");

        return false;
    });

    $("#vistaAdjuntos").on("click","a", function (e) {
        e.preventDefault();

        $(".ventanaAdjuntos iframe")
            .attr("src","")
            .attr("src","http://sicalsepcon.net/ibis/public/documentos/notas_ingreso/adjuntos/"+$(this).attr("href"));
        
        return false;
    });

})