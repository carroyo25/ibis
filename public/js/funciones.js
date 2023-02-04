$("#esperar").fadeOut();

var HOST = $(location).attr('hostname');

if (HOST ==  "localhost") {
    var RUTA = "http://localhost/ibis/";
}else if (HOST ==  "192.168.110.16"){
    var RUTA = "http://192.168.110.16/ibis/";
}else if (HOST ==  "200.41.86.58"){
    var RUTA = "http://200.41.86.58/ibis/";
}else if (HOST ==  "sicalsepcon.net"){
    var RUTA = "http://sicalsepcon.net/ibis/";
}else if (HOST ==  "200.115.23.164"){
    var RUTA = "http://200.115.23.164/ibis/";
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

        $(this).find('td').eq(idx).text($.strPad(item,3));
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

checkCantTablesMinMax = (table,idx) =>{
    let sw = false;

    table.each(function(){
        if ($(this).find('td').eq(idx).children().val() < 1  || $(this).find('td').eq(idx).children().val() > 5){
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

sumarTotales = (table) => {
    let suma = 0;

    table.each(function(){
        let valor = $(this).data("total");
        suma = suma + valor;
    });

    return suma;
}


fechaActual = () => {
    var d = new Date(); 
    var month = d.getMonth()+1; 
    var day = d.getDate(); 
    var output = d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day;

    return output;
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

validarExtension = (fileUpload) =>{
    // Obtener nombre de archivo
    let archivo = fileUpload.value,
    // Obtener extensión del archivo
        extension = archivo.substring(archivo.lastIndexOf('.'),archivo.length);
    // Si la extensión obtenida no está incluida en la lista de valores
    // del atributo "accept", mostrar un error.
    if(fileUpload.getAttribute('accept').split(',').indexOf(extension) < 0) {
        return true;
    }else {
        return false;
    }
}


formatoNumeroConComas = (number,decimals,dec_point,thousands_point) =>{
    if (number == null || !isFinite(number)) {
        throw new TypeError("number is not valid");
    }

    if (!decimals) {
        var len = number.toString().split('.').length;
        decimals = len > 1 ? len : 0;
    }

    if (!dec_point) {
        dec_point = '.';
    }

    if (!thousands_point) {
        thousands_point = ',';
    }

    number = parseFloat(number).toFixed(decimals);

    number = number.replace(".", dec_point);

    var splitNum = number.split(dec_point);
    splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
    number = splitNum.join(dec_point);

    return number;
}

