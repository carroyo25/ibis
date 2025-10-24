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

    $("#btnConsulta").click(function(e){
        e.preventDefault();

        $(".carpetas").empty();
        $("#esperar").fadeIn();

        let formData = new FormData();
        formData.append("user",$("#id_user").val());
        formData.append("orden",$("#ordenSearch").val());

        fetch(RUTA+"certificados/filtroCertificado",{
            method:'POST',
            body:formData
        })
        .then(response =>response.json())
        .then(data =>{
             $("#esperar").fadeOut();

            data.datos.forEach(e =>{
                const li = document.createElement('li');
                const a = document.createElement('a');

                a.setAttribute('href',e.nidrefer);
                a.innerHTML = `<i class="fas fa-folder-open"></i><p>${e.cnumero} ${e.cper} ${e.ccodproy}</p>`;

                li.appendChild(a);
                $(".carpetas").append(li);
            })
        })
        .catch (error=>{
            console.log(error.message);
        })

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