const $ = document;
const bancos = $.getElementById("agregar_bancos");
const tabla_bancos = $.getElementById("tabla_bancos");
const tabla_bancos_body = $.getElementById("tabla_bancos_body");
const btn_guardar = $.getElementById("btn_guardar");
const requerido = $.querySelectorAll(".requerido");

const ruc = $.getElementById("ruc");
const razon_alta = $.getElementById("razon_social");
const direccion_alta = $.getElementById("direccion");
const ubigeo_alta = $.getElementById("ubigeo");

//inicializa par la notificacion
let notifier = new AWN(),
    errorCantEnti = false;


bancos.onclick = (e) => {
  e.preventDefault();

  let row = `<tr>
                <td>
                  <select name="entidadedFinancieras" id="entidadedFinancieras">
                      <optgroup label="Empresas Bancarias">
                        <option value="volvo">Banco de Comercio</option>
                        <option value="11">Banco de Crédito del Perú</option>
                        <option value="">Banco Interamericano de Finanzas (BanBif)</option>
                        <option value="">Banco Pichincha</option>
                        <option value="15">BBVA</option>
                        <option value="12">Interbank</option>
                        <option value="">MiBanco</option>
                        <option value="13">Scotiabank Perú</option>
                        <option value="">Banco Falabella</option>
                        <option value="">Banco Ripley</option>
                        <option value="">Banco Santander Perú</option>
                        <option value="">ICBC PERU BANK</option>
                      </optgroup>
                      <optgroup label="Empresas Financieras">
                        <option value="">Crediscotia</option>
                        <option value="">Confianza</option>
                        <option value="">Credinka</option>
                        <option value="">Mitsui Auto Finance</option>
                        <option value="">Oh!</option>
                      </optgroup>
                      <optgroup label="Cajas Municipales de Ahorro y Crédito (CMAC)">
                        <option value="17">Arequipa</option>
                        <option value="18">Cusco</option>
                        <option value="">Del Santa</option>
                        <option value="">Trujillo</option>
                        <option value="16">Huancayo</option>
                        <option value="">Ica</option>
                        <option value="">Piura</option>
                        <option value="">Tacna</option>
                      </optgroup>
                      <optgroup label="Cajas Municipales de Crédito y Popular (CMCP)">
                        <option value="">Caja Metropolitana de Lima</option>
                      </optgroup>
                      <optgroup label="Empresas de Crédito">
                         <option value="">Volvo Financial Services</option>
                         <option value="">Inversiones La Cruz</option>
                         <option value="">Santander Consumer Perú</option>
                         <option value="">TOTAL, Servicios Financieros</option>
                      </optgroup>
                  </select>
                </td>
                <td>
                  <select>
                    <option value="-1">Seleccione una opcion</option>
                    <option value="20">SOLES</option>
                    <option value="21">DOLARES</option>
                  </select>
                </td>
                <td>
                  <select>
                    <option value="-1">Seleccione una opcion</option>
                    <option value="01">AHORROS</option>
                    <option value="02">CUENTA CORRIENTE</option>
                    <option value="02">INTERBANCARIA</option>
                  </select>
                </td>
                <td><input type="text"></td>
                <td><a href="" data-grabado="0" data-idx=""><i class="fas fa-trash-alt"></i></a></td>
            </tr>`;

  tabla_bancos_body.insertRow(-1).outerHTML = row;

  return false;
}

btn_guardar.onclick = (e) => {
  e.preventDefault();

  let contador = 0;

  requerido.forEach((campo)=>{
    let item = campo.getAttribute("id");
    if ( campo.value == "" ){
      $.getElementById(item).classList.add("obligatorio");
      contador++;
    }
  })

  try {
    if ( contador > 0 ) throw new Error('Hay campos sin rellenar');

    const datos = new URLSearchParams(new FormData(document.getElementById("datos_entidad")));
    datos.append("funcion","grabar");

    fetch ('inc/login.inc.php',{
      method: 'POST',
      body: datos
    })
    .then(response => response.json())
    .then(data => {
        /*if (data.respuesta) {
            window.location = data.pagina;
        }else{
             mostrarMensaje(data.mensaje,data.clase);
        }*/
    })
  .catch(error => {
      console.error(error.message);
  })

  } catch (error) {
    notifier.alert(error.message);
  }

  return false;
}

$.addEventListener("change", (e)=>{
  let item = e.target.getAttribute("id");
  if ( e.target.value != "" ){
    $.getElementById(item).classList.remove("obligatorio");
  }
})


ruc.onkeypress = (e) => {
    if (e.key === "Enter") {
      let ruc_valor = ruc.value,
        formData = new FormData();

      formData.append('ruc',ruc_valor);
      formData.append('funcion','getEntiByRuc');

      try {
          if ( !validar(ruc) ) throw new Error("El RUC ingresado es incorrecto..."); 

          fetch('consultas.php',{
            method: 'POST',
            body: formData
          })
          .then(response=>response.json())
          .then(data=>{
             if (data.length >= 1){
              notifier.alert('Ya encuentra registrado...');
              errorCantEnti = true;
             };
          })
      } catch (error) {
        notifier.alert(error.message);
      }
    }
}


/***funciones ****/
const validar = (input) =>{
  let ruc = input.value.replace(/[-.,[\]()\s]+/g,""),
      valido = false;
  
  //Es entero? 
  if ((ruc = Number(ruc)) && ruc % 1 === 0	&& rucValido(ruc)) { // ⬅️ Acá se comprueba
  	valido = true;
  }

  return valido;
}

const rucValido =(ruc) =>{
   //11 dígitos y empieza en 10,15,16,17 o 20
   if (!(ruc >= 1e10 && ruc < 11e9
    || ruc >= 15e9 && ruc < 18e9
    || ruc >= 2e10 && ruc < 21e9))

     return false;
 
  for (var suma = -(ruc%10<2), i = 0; i<11; i++, ruc = ruc/10|0)
      suma += (ruc % 10) * (i % 7 + (i/7|0) + 1);

  return suma % 11 === 0;
}