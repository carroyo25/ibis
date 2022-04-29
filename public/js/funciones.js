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

numberWithCommas = (x) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

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

checkCantTables = (table,idx) =>{
    let sw = false;

    table.each(function(){
        if ($(this).find('td').eq(idx).children().val() == 0){
            sw = true;
        }
    })

    return sw;
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

diferenciadefechas = (inicio,final) => {
    var entrega = new Date(inicio).getTime();
    var actual  = new Date(final).getTime();

    diff = (entrega - actual)/(1000*60*60*24);

    return diff;
}


//queda pendiente el drag and drop 
dropHandler = (ev) => {
    console.log('Fichero(s) arrastrados');
  
    // Evitar el comportamiendo por defecto (Evitar que el fichero se abra/ejecute)
    ev.preventDefault();
  
    if (ev.dataTransfer.items) {
      // Usar la interfaz DataTransferItemList para acceder a el/los archivos)
      let fragment = "";
      for (var i = 0; i < ev.dataTransfer.items.length; i++) {
        // Si los elementos arrastrados no son ficheros, rechazarlos
        if (ev.dataTransfer.items[i].kind === 'file') {
          var file = ev.dataTransfer.items[i].getAsFile();
          //console.log('... file[' + i + '].name = ' + file.name);
          fragment +=`<li><p><i class="far fa-file"></i></p><p>${file.name}</p></li>`;
        }
      }
    } else {
      // Usar la interfaz DataTransfer para acceder a el/los archivos
      for (var i = 0; i < ev.dataTransfer.files.length; i++) {
        console.log('... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
      }
    }
  
    // Pasar el evento a removeDragData para limpiar
    removeDragData(ev)
  }

 dragOverHandler = (ev) => {
    console.log('File(s) in drop zone');
  
    // Prevent default behavior (Prevent file from being opened)
    ev.preventDefault();
  }

removeDragData = (ev) =>{
    console.log('Removing drag data')
  
    if (ev.dataTransfer.items) {
      // Use DataTransferItemList interface to remove the drag data
      ev.dataTransfer.items.clear();
    } else {
      // Use DataTransfer interface to remove the drag data
      ev.dataTransfer.clearData();
    }
}


