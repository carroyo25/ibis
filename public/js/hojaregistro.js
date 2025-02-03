const $ = document;
const wrap = $.getElementById("wrap");
const bancos = $.getElementById("agregar_bancos");
const tabla_bancos = $.getElementById("tabla_bancos");
const tabla_bancos_body = $.getElementById("tabla_bancos_body");
const btn_guardar = $.getElementById("btn_guardar");
const requerido = $.querySelectorAll(".requerido");
const enlaceBancos = tabla_bancos_body.getElementsByTagName("a")

const upFloat = $.getElementById("floatUp");
const saveFloat = $.getElementById("floatSave");
const cancelReg = $.getElementById("cancelReg");

const ruc = $.getElementById("ruc");
const razon_alta = $.getElementById("razon_social");
const direccion_alta = $.getElementById("direccion");
const ubigeo_alta = $.getElementById("ubigeo");

//inicializar para la notificacion
let notifier = new AWN(),
    errorCantEnti = false,
    errorMail = false;


$.addEventListener("click",(e)=>{
  if (e.target.matches(".btnSave *")){
    let contador = 0;

      //verificar los campos en blanco
      requerido.forEach((campo)=>{
        let item = campo.getAttribute("id");

        if ( campo.value == "" ){
          $.getElementById(item).classList.add("obligatorio");
          $.getElementById(item).setAttribute("placeholder", "Este campo debe ser rellenado");
          contador++;
        }
      })

      try {
        
        if ( contador > 0 ) throw new Error('Hay campos sin rellenar');
        /*if ( !validar(ruc) ) throw new Error("El RUC ingresado es incorrecto...");*/

        const form = $.querySelector('#datos_entidad')
        const datos = new FormData(form);


        datos.append("funcion","grabarProveedor");

        
        datos.append("bancos",JSON.stringify(detalleBancos()));

        notifier.async(
          fetch ('procesos.php',{
            method: 'POST',
            body: datos
          })
          .then(response => response.json())
          .then(data => {
              limpiarFormato();
          }),'',undefined,'Procesando'
        )
      .catch(error => {
          console.error(error.message);
      })
      } catch (error) {
        notifier.alert(error.message);
      }

      return false;
  }
})

$.addEventListener("change", (e)=>{
  let item = e.target.getAttribute("id");

  if ( e.target.matches(".obligatorio") ){
      //Para cambiar el estado de los campos obligatorios    
      if ( e.target.value != ""){
        $.getElementById(item).classList.remove("obligatorio");
      }
  }else if ( e.target.matches(".correo") ){
      if ( !validarCorreo(e.target.value) ) {
          notifier.alert("Formato de correo inválido");
      }else{
        e.target.classList.add("valido")
      }
  }
})
   
bancos.onclick = (e) => {
  e.preventDefault();

  let row = `<tr>
                <td>
                  <select name="entidadedFinancieras" id="entidadedFinancieras">
                      <optgroup label="Empresas Bancarias">
                        <option value="303">Banco de Comercio</option>
                        <option value="11">Banco de Crédito del Perú</option>
                        <option value="12">Interbank</option>
                        <option value="13">Scotiabank Perú</option>
                        <option value="14">Banco de la Nación</option>
                        <option value="15">BBVA</option>
                        <option value="281">Banco Interamericano de Finanzas (BanBif)</option>
                        <option value="282">Banco Pichincha</option>
                        <option value="283">MiBanco</option>
                        <option value="284">Banco Falabella</option>
                        <option value="285">Banco Ripley</option>
                        <option value="286">Banco Santander Perú</option>
                        <option value="287">ICBC PERU BANK</option>
                      </optgroup>
                      <optgroup label="Empresas Financieras">
                        <option value="288">Crediscotia</option>
                        <option value="289">Confianza</option>
                        <option value="290">Credinka</option>
                        <option value="291">Mitsui Auto Finance</option>
                        <option value="292">Oh!</option>
                      </optgroup>
                      <optgroup label="Cajas Municipales de Ahorro y Crédito (CMAC)">
                        <option value="16">Huancayo</option>
                        <option value="17">Arequipa</option>
                        <option value="18">Cusco</option>
                        <option value="293">Del Santa</option>
                        <option value="294">Trujillo</option>
                        <option value="295">Ica</option>
                        <option value="296">Piura</option>
                        <option value="297">Tacna</option>
                      </optgroup>
                      <optgroup label="Cajas Municipales de Crédito y Popular (CMCP)">
                        <option value="298">Caja Metropolitana de Lima</option>
                      </optgroup>
                      <optgroup label="Empresas de Crédito">
                         <option value="299">Volvo Financial Services</option>
                         <option value="300">Inversiones La Cruz</option>
                         <option value="301">Santander Consumer Perú</option>
                         <option value="302">TOTAL, Servicios Financieros</option>
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
                    <option value="282">AHORROS</option>
                    <option value="283">CUENTA CORRIENTE</option>
                    <option value="284">INTERBANCARIA</option>
                  </select>
                </td>
                <td><input type="text"></td>
                <td><a href="#" data-grabado="0" data-idx=""><i class="fas fa-trash-alt lnkTrash"></i></a></td>
            </tr>`;

  tabla_bancos_body.insertRow(-1).outerHTML = row;

  return false;
}


tabla_bancos_body.addEventListener("click",(e)=>{
  e.preventDefault();

  if (e.target.matches (".lnkTrash")){
    e.target.closest("tr").remove();
  }
  
  return false;
});

ruc.onkeypress = (e) => {
    if (e.key === "Enter") {
      let ruc_valor = ruc.value,
        formData = new FormData();

      formData.append('ruc',ruc_valor);
      formData.append('funcion','getEntiByRuc');

      try {
          /*if ( !validar(ruc) ) throw new Error("El RUC ingresado es incorrecto...");*/

          e.target.classList.add('valido');

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

upFloat.onclick = (e) => {
  wrap.scrollTo(0, 0);
}

cancelReg.onclick = (e) => {
  e.preventDefault();

  limpiarFormato();

  return false;
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

const rucValido = (ruc) =>{
   //11 dígitos y empieza en 10,15,16,17 o 20
   if (!(ruc >= 1e10 && ruc < 11e9
    || ruc >= 15e9 && ruc < 18e9
    || ruc >= 2e10 && ruc < 21e9))

     return false;
 
  for (var suma = -(ruc%10<2), i = 0; i<11; i++, ruc = ruc/10|0)
      suma += (ruc % 10) * (i % 7 + (i/7|0) + 1);

  return suma % 11 === 0;
}

const validarCorreo = (correo) => {
  let validRegexEmail = /^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/;

  if( validRegexEmail.test(correo) ){
		//alert('Email is valid, continue with form submission--> '+ correo);
		return true;
	}else{
		//alert('Email is invalid, skip form submission--> '+ correo);
		return false;
	}
}

const detalleBancos = () => {
  const fila = document.querySelector("#tabla_bancos_body").getElementsByTagName("tr");

  let nreg = fila.length,
        DATOS = [];

    for (let i = 0; i < nreg; i++) {
        let dato = {}

        dato['idbanco']   = fila[i].cells[0].children[0].value;
        dato['idmoneda']  = fila[i].cells[1].children[0].value;
        dato['idcuenta']  = fila[i].cells[2].children[0].value;
        dato['nrocuenta'] = fila[i].cells[3].children[0].value;

        DATOS.push(dato);
    }

    return DATOS;   
}

const limpiarFormato = () => {
  document.getElementById("datos_entidad").reset();
  document.getElementById("tabla_bancos_body").innerHTML = "";
}