$(function(){
    $("#esperar").fadeOut();

    $("#btnConsulta").on('click', function(e) {
        e.preventDefault();

        $("#esperar").fadeIn();

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"valorizado/consulta", str,
            function (data, text, requestXHR) {
                $("#esperar").fadeOut();
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

        console.log(JSON.stringify(array));

        return false;
    });
})