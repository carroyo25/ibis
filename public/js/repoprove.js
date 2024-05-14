$(function() {
    let campo = "",
        ffemision = [],
        cCostos = [],
        cEntidad = [];
    
    $("#espera").fadeOut();

    $(".contenedorfiltro *").click(function(e){
        e.preventDefault();

        let control = $(this);
        
        campo = $(this).parent().parent().data("campo");
        
        $(".filter_options").fadeOut();

        llamarFiltro(control,campo);

        return false;
    });

    $(".btn_sendfilter").click(function (e) { 
        e.preventDefault();
    
        let indice = 0;
    
        $('.filterList input[type=checkbox]:checked').each(function() {
            if (campo == 'ffemision')
                ffemision[indice++] = $(this).attr("id");
            else if (campo == 'cCostos')
                cCostos[indice++] = $(this).attr("id");
            else if (campo == 'cEntidad')
                cEntidad[indice++] = $(this).attr("id");
        });
    
        $(this).parent().fadeOut();

        console.log(ffemision,cCostos,cEntidad);
    
        return false;
    });
    
})


llamarFiltro = (control,campo) => {
    $(".filter_options").children('ul').empty();

    let formdata = new FormData();
    formdata.append("campo",campo);

    fetch(RUTA+"repoprove/consultarValoresLista",{
        method: "POST",
        body: formdata
    })
    .then(reponse => reponse.json())
    .then(data => {
        data.valores.forEach(valor => {
            let item = ` <li><input type="checkbox" id="${valor['id']}"> ${valor['onumero']} </li>`;
            $(".filter_options").children('ul').append(item);
        });

        control.parent().parent().children(".filter_options").fadeToggle();
    });
}

