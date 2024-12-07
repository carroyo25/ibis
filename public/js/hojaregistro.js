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


bancos.onclick = (e) => {
  e.preventDefault();

  let row = `<tr>
                <td>
                  <select name="entidadedFinancieras" id="entidadedFinancieras">
                      <optgroup label="Empresas Bancarias">
                        <option value="volvo">Banco de Comercio</option>
                        <option value="volvo">Banco de Crédito del Perú</option>
                        <option value="saab">Banco Interamericano de Finanzas (BanBif)</option>
                        <option value="volvo">Banco Pichincha</option>
                        <option value="volvo">BBVA</option>
                        <option value="saab">Interbank</option>
                        <option value="volvo">MiBanco</option>
                        <option value="volvo">Scotiabank Perú</option>
                        <option value="volvo">Banco Falabella</option>
                        <option value="volvo">Banco Ripley</option>
                        <option value="saab">Banco Santander Perú</option>
                        <option value="saab">ICBC PERU BANK</option>
                      </optgroup>
                      <optgroup label="Empresas Financieras">
                        <option value="volvo">Crediscotia</option>
                        <option value="volvo">Confianza</option>
                        <option value="volvo">Credinka</option>
                        <option value="volvo">Mitsui Auto Finance</option>
                        <option value="volvo">Oh!</option>
                      </optgroup>
                      <optgroup label="Cajas Municipales de Ahorro y Crédito (CMAC)">
                        <option value="volvo">Arequipa</option>
                        <option value="volvo">Cusco</option>
                        <option value="volvo">Del Santa</option>
                        <option value="volvo">Trujillo</option>
                        <option value="volvo">Huancayo</option>
                        <option value="volvo">Ica</option>
                        <option value="volvo">Cusco</option>
                        <option value="volvo">Piura</option>
                        <option value="volvo">Tacna</option>
                      </optgroup>
                      <optgroup label="Cajas Municipales de Crédito y Popular (CMCP)">
                        <option value="volvo">Caja Metropolitana de Lima</option>
                      </optgroup>
                      <optgroup label="Empresas de Crédito">
                         <option value="volvo">Volvo Financial Services</option>
                         <option value="volvo">Inversiones La Cruz</option>
                         <option value="volvo">Santander Consumer Perú</option>
                         <option value="volvo">TOTAL, Servicios Financieros</option>
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
  } catch (error) {
    console.log(error.message);
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
      let ruc_valor = ruc.value;

      const requestOptions = {
        method: "GET",
        redirect: "follow"
      };
      
      fetch("https://dniruc.apisperu.com/api/v1/ruc/"+ruc_valor+"?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImNhYXJyb3lvQGhvdG1haWwuY29tIn0.8qOPsmbIXb6G5eTo1OQ8CJXKDisde7LItI2faTRSeoE", requestOptions)
        .then((response) => response.json())
        .then((result) => {
            //console.log(result);

            razon_alta.value = result.razonSocial;
            direccion_alta.value = result.direccion;
            ubigeo_alta.value = result.ubigeo;
        })
        .catch((error) => console.error(error));

    }
}