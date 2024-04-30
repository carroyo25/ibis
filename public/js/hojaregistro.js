const $ = document;

ruc = $.getElementById("ruc");

console.log('Alta de proveedores');

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
            $.getElementById('razon_social').value = result.razonSocial;
            $.getElementById('direccion').value = result.direccion;
        })
        .catch((error) => console.error(error));

    }
}
