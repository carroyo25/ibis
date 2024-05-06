const $ = document;
const bancos = $.getElementById("agregar_bancos");
const tabla_bancos = $.getElementById("tabla_bancos");
const tabla_bancos_body = $.getElementById("tabla_bancos_body");
const btn_guardar = $.getElementById("btn_guardar");
const requerido = $.querySelectorAll("input");

ruc = $.getElementById("ruc");

bancos.onclick = (e) => {
  e.preventDefault();

  let row = `<tr>
                <td>
                  <select>
                    <option value="-1">Seleccione una opcion</option>
                    <option value="11">BANCO DE CREDITO</option>
                    <option value="12">INTERBANK</option>
                    <option value="13">SCOTIA BANK</option>
                    <option value="15">BANCO CONTINENTAL</option>
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

  requerido.forEach((campo)=>{
    let item = campo.getAttribute("id");
    if ( campo.value == "" ){
      $.getElementById(item).classList.add("obligatorio");
    }
  })

  return false;
}

/*requerido.addEventListener("keydown", (e) => {
  let item = campo.getAttribute("id");
  if ( campo.value != "" ){
      $.getElementById(item).classList.remove("obligatorio");
  }
})*/


/*ruc.onkeypress = (e) => {
    if (e.key === "Enter") {
      let ruc_valor = ruc.value;

      const requestOptions = {
        method: "GET",
        redirect: "follow"
      };

      fetch("https://dniruc.apisperu.com/api/v1/ruc/"+ruc_valor+"?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImNhYXJyb3lvQGhvdG1haWwuY29tIn0.8qOPsmbIXb6G5eTo1OQ8CJXKDisde7LItI2faTRSeoE", requestOptions)
        .then((response) => response.json())
        .then((result) => {
            $.getElementById('razon_social').value = result.razonSocial;
            $.getElementById('direccion').value = result.direccion;
        })
        .catch((error) => console.error("error getting"));

    }
}*/