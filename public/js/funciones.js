var HOST = $(location).attr('hostname');

if (HOST ==  "localhost") {
    var RUTA = "http://localhost/ibis/";
}else {
    var RUTA = "http://200.41.86.61:3000/ibis/";
}

var emailreg = /^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/;
var numeros = /^[0-9]*\.?[0-9]*$/;
var fecha = /^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/;
var ruc = /^[0-9]{11}$/;

$.strPad = function(i,l,s) {
    var o = i.toString();
    if (!s) { s = '0'; }
    while (o.length < l) {
        o = s + o;
    }
    return o;
};

mostrarMensaje = (mensaje,clase) => {
    $(".mensaje p")
        .empty()
        .text(mensaje);

    $(".mensaje")
        .removeClass('mensaje_error, mensaje_correcto')
        .addClass(clase)
        .css('right','0');
    
    setTimeout(function() {
        $(".mensaje").css('right',"-100%");
    },2500);

}

fillTables = (table,idx) => {
    var item = 0;

    table.each(function(){
        item++;

        $(this).find('td').eq(idx).text($.strPad(item,2));
    });
}

checkExistTable = (table,item,indice) => {
    var itenExist = false;

    table.each(function(){
        let itemTable = $(this).find('td').eq(indice).text();

        if (itemTable == item) {
            itenExist = true;
            return false;
        }
    })

    return itenExist;
}

buscar = (_this) => {
    $.each($("#tablaPrincipal tbody tr"), function() {
        if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
            $(this).hide();
        else
            $(this).show();
        });
}

previewImg = (event,img) => {
    $('#foto').attr('src', event.target.result)
    	.attr('width', '300px')
    	.attr('height', '250px');
};


