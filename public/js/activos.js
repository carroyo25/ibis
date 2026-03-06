$(function () {
  $("#esperar").css({ display: "none", opacity: "0" });

  const modal_registro = document.getElementById("dialogo_registro");

  const btnRegister = document.getElementById("nuevoRegistro");
  const btnExport = document.getElementById("excelFile");
  const btnCancelDialog = document.getElementById("btnCancelarDialogoActivos");
  const btnSave = document.getElementById("btnGrabarDialogoActivos");

  const inputSearchCode = document.getElementById("codigoSearch");
  const inputSerie = document.getElementById("serie");
  const inputItemCode = document.getElementById("codigo_interno");
  const inputCalibra = document.getElementById("fecha_calibra");
  const inputEstado = document.getElementById("estado_actual");
  const inputUbicacion = document.getElementByI('ubicacion');

  const sltCostos = document.getElementById("centro_costos");
  const sltFrecuencia = document.getElementById("frecuencia");

  const fmrActivos = document.getElementById("activos_form");

  btnRegister.addEventListener("click", (e) => {
    e.preventDefault();

    //llama el codigo del usuario que registra
    document.getElementById("codigo_usuario").value = document.getElementById("id_user").value;

    limpiarFormulario(true);
    
    modal_registro.style.display = "block";

    return false;
  });

  btnCancelDialog.addEventListener("click", (e) => {
    e.preventDefault();

    limpiarFormulario(true);
    modal_registro.style.display = "none";

    return false;
  });

  //busca el item en el centro de costos
  inputSearchCode.addEventListener("keydown", (e) => {
    if (e.key == "Enter") {
      try {
        if (sltCostos.value == -1)
          throw new Error("Seleccione un centro de costos");
        if (e.target.value == "")
          throw new Error("Escriba un codigo para validar");

        const formData = new FormData();
        formData.append("codigo", e.target.value);
        formData.append("costos", sltCostos.value);

        fetch(RUTA + "activos/buscaCodigo", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            document.getElementById("descripSearch").value =
              data.datos[0]["descripcion"];
            document.getElementById("unidad").value = data.datos[0]["cabrevia"];
            document.getElementById("codigo_interno").value =
              data.datos[0]["id_cprod"];
            document.getElementById("codigo_unidad").value =
              data.datos[0]["ncodmed"];

            mostrarMensaje("👌 Codigo existente", "mensaje_correcto");
          });
      } catch (error) {
        mostrarMensaje(error.message, "mensaje_error");
        document.getElementById("codigo_interno").value = "";
      }
    }
  });

  //buscar si esta asignado
  inputSerie.addEventListener("keydown", (e) => {
    if (e.key == "Enter") {
      try {
        if (sltCostos.value == -1)
          throw new Error("Seleccione un centro de costos");
        if (inputItemCode.value == "")
          throw new Error("Seleccione un codigo de producto para validar");
        if (e.target.value == "")
          throw new Error("Escriba una serie para validar");

        const formData = new FormData();
        formData.append(
          "codigo",
          document.getElementById("codigo_interno").value,
        );
        formData.append("costos", sltCostos.value);
        formData.append("serie", e.target.value);

        fetch(RUTA + "activos/asignados", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if ( data.existe ){
              mostrarMensaje("La serie ya se encuentra registrada..");
              
              return false;
            }
            if (data.asignado) {
              document.getElementById("dni").value = data.datos[0]["dni"];
              document.getElementById("cargo").value =
                data.datos[0]["cargo"].toUpperCase();
              document.getElementById("nombres").value =
                data.datos[0]["nombres"] +
                " " +
                data.datos[0]["paterno"] +
                " " +
                data.datos[0]["materno"];

              document.getElementById("fecha_asigna").value = data.salida;

              document.getElementById("ubicacion").value = "ASIGNADO";
              document.getElementById("ubicacion").style.color = "#c62828";
              document.getElementById("ubicacion").style.backgroundColor =
                "#ffebee";
            } else {
              document.getElementById("dni").value = "";
              document.getElementById("cargo").value = "";
              document.getElementById("nombres").value = "";

              document.getElementById("ubicacion").value = "ALMACEN";
              document.getElementById("ubicacion").style.color = "#2e7d32";
              document.getElementById("ubicacion").style.backgroundColor =
                "#ffebee";
            }
          });
      } catch (error) {
        mostrarMensaje(error.message, "mensaje_error");
      }
    }
  });

  inputCalibra.addEventListener("change", (e) => {
    calcularVencimiento();
    return false;
  });

  sltFrecuencia.addEventListener("change", (e) => {
    calcularVencimiento();
  });

  btnSave.addEventListener("click", (e) => {
    e.preventDefault();

    $("#esperar").css({ display: "block", opacity: "1" });

    const formData = new FormData(fmrActivos);

    fetch(RUTA + "activos/registro", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        
        limpiarFormulario(false);

        mostrarMensaje(data.mensaje, data.clase);

        $("#esperar").css({ display: "none", opacity: "0" });
      });

    return false;
  });

  inputEstado.addEventListener('change', (e) =>{
    e.preventDefault();

    e.target.style.color = "#000";
    e.target.style.backgroundColor  = "#fff";

    document.getElementById("observa_estado").value = "";

    return false;
  });

   inputUbicacion.addEventListener('change', (e) =>{
    e.preventDefault();

    e.target.style.color = "#000";
    e.target.style.backgroundColor  = "#fff";

    return false;
  })
});

// Función para calcular la fecha de vencimiento
function calcularVencimiento_old() {
  const fechaRevision = document.getElementById("fecha_calibra").value;
  const periodo = document.getElementById("frecuencia").value;
  const fechaVencimiento = document.getElementById("vence_calibra");

  if (fechaRevision) {
    // Crear objeto fecha
    const fecha = new Date(fechaRevision);

    // Determinar días a sumar según el período
    let diasASumar;
    if (periodo === "anual") {
      diasASumar = 365; // Año
    } else {
      diasASumar = 180; // Semestre (aproximadamente 6 meses)
    }

    // Sumar los días
    fecha.setDate(fecha.getDate() + diasASumar);

    // Formatear la fecha para el input (YYYY-MM-DD)
    const año = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const dia = String(fecha.getDate()).padStart(2, "0");

    fechaVencimiento.value = `${año}-${mes}-${dia}`;
  } else {
    fechaVencimiento.value = "";
  }
}

function actualizarEstado(fechaVenc) {
  const estadoInput = document.getElementById("estado_actual");
  const observaciones = document.getElementById("observa_estado");

  if (!fechaVenc) {
    estadoInput.value = "";
    estadoInput.style.color = "#555";
    estadoInput.style.backgroundColor = "#f0f0f0";
    observaciones.value = "";
    return;
  }

  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  const vencimiento = new Date(fechaVenc + "T00:00:00");
  const diffMs = vencimiento - hoy;
  const diffDias = Math.abs(Math.round(diffMs / (1000 * 60 * 60 * 24)));

  if (hoy < vencimiento) {
    estadoInput.value = "CALIBRADO";
    estadoInput.style.color = "#2e7d32";
    estadoInput.style.backgroundColor = "#e8f5e9";
    observaciones.value = `Faltan ${diffDias} día(s) para vencer.`;
  } else {
    estadoInput.value = "VENCIDO";
    estadoInput.style.color = "#c62828";
    estadoInput.style.backgroundColor = "#ffebee";
    observaciones.value = `Venció hace ${diffDias} día(s).`;
  }
}

function calcularVencimiento() {
  const fechaRevision = document.getElementById("fecha_calibra").value;
  const periodo = document.getElementById("frecuencia").value;
  const fechaVencimiento = document.getElementById("vence_calibra");

  if (fechaRevision) {
    const fecha = new Date(fechaRevision);
    let diasASumar = periodo === "303" ? 365 : 180;
    fecha.setDate(fecha.getDate() + diasASumar);

    const año = fecha.getFullYear();
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const dia = String(fecha.getDate()).padStart(2, "0");
    const fechaCalc = `${año}-${mes}-${dia}`;
    fechaVencimiento.value = fechaCalc;
    actualizarEstado(fechaCalc); // 👈 llama al estado automático
  } else {
    fechaVencimiento.value = "";
    actualizarEstado("");
  }
}

function limpiarFormulario(sw) {
  if ( sw ) {
    document.getElementById("codigo_interno").value = "";
    document.getElementById("centro_costos").value = "-1";
    document.getElementById("codigoSearch").value = "";
    document.getElementById("descripSearch").value = "";
  } 
  
  document.getElementById("unidad").value = "";
  document.getElementById("cantidad").value = "1";
  document.getElementById("serie").value = "";
  document.getElementById("marca").value = "";
  document.getElementById("modelo").value = "";
  document.getElementById("dni").value = "";
  document.getElementById("nombres").value = "";
  document.getElementById("cargo").value = "";
  document.getElementById("area").value = "";
  document.getElementById("fecha_asigna").value = "";
  document.getElementById("frecuencia").value = "303";
  document.getElementById("fecha_calibra").value = "";
  document.getElementById("vence_calibra").value = "";
  document.getElementById("estado_actual").value = "";
  document.getElementById("observa_estado").value = "";
  document.getElementById("guia_envio").value = "";
  document.getElementById("fecha_envio").value = "";
  document.getElementById("guia_recepcion").value = "";
  document.getElementById("fecha_recepcion").value = "";
  document.getElementById("ubicacion").value = "";
  document.getElementById("contenedor").value = "";
  document.getElementById("estante").value = "";
  document.getElementById("letra").value = "";
  document.getElementById("columna").value = "";

  document.getElementById("estado_actual").style.color = "#000";
  document.getElementById("estado_actual").style.backgroundColor  = "#fff";

  document.getElementById("ubicacion").style.color = "#000";
  document.getElementById("ubicacion").style.backgroundColor  = "#fff";


}
