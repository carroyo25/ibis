$(() => {
    $("#btnConsulta").click(function(e){

        let str = $("#formConsulta").serialize();

        $.post(RUTA+"valitem/consulta",str,
            function (data, textStatus, jqXHR) {
                $("#tablaPrincipal tbody")
                    .empty()
                    .append(data);
            },
            "text"
        );
    });

    $("#excelFile").click(function(e){
        e.preventDefault();

        var array = [];
        /* Obtenemos todos los tr del Body*/
        var rowsBody= $("#tablaPrincipal").find('tbody > tr');
        /* Obtenemos todos los th del Thead */
        var rowsHead= $("#tablaPrincipal").find('thead > tr > th');
        
        /* Iteramos sobre as filas del tbody*/
        for (var i = 0; i < rowsBody.length; i++) {
            var obj={};/* auxiliar*/
            for (var j = 0;j < rowsHead.length;j++) /*  Iteramos sobre los th de THead*/
                /*Asignamos como clave el text del th del thead*/
                /*Asignamos como Valor el text del tr del tbody*/
                obj[rowsHead[j].dataset.titulo] =  rowsBody[i].getElementsByTagName('td')[j].innerText;
            
            array.push(obj);/* Añadimos al Array Principal*/
        }

        $.post(RUTA+"valitem/exportar", {detalles:JSON.stringify(array)},
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
            },
            "json"
        );

        return false;
    });
})

