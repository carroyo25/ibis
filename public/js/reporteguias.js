(async () => {
  const totalItems = await contarItems("reporteguias/itemsConsulta");
  const btns = document.getElementsByClassName("page-btn");

  let itemsPorPagina = itemsPorPantalla();
  let totalPaginas = Math.ceil(totalItems / itemsPorPagina);
  let currentPage = 1;

  crearBotonesPaginacion(totalPaginas, 1, 30);

  $("#esperar").css({ display: "none" });

  document.addEventListener("click", (e) => {
    if (e.target.matches(".page-btn")) {
      e.preventDefault();

      currentPage = parseInt(e.target.innerHTML);
      cleanActives();

      e.target.classList.add("active");

      return false;
    } else if (e.target.matches(".next-page")) {
      const lastInView = btns[btns.length - 1].textContent;

      if ( currentPage === parseInt(lastInView) ){
        crearBotonesPaginacion(totalPaginas, parseInt(lastInView)+1, parseInt(lastInView)+30);
        currentPage = 1;
      }else{
        cleanActives();
        currentPage++;
        btns[currentPage-1].classList.add("active");
      }
    } else if (e.target.matches(".first-page")) {
      if ( currentPage > 1 ){
        cleanActives();
        currentPage--;
        //btns[currentPage-1].classList.add("active");
      }
    }
  });
})();

async function contarItems(ruta_consulta) {
  try {
    const response = await fetch(RUTA + ruta_consulta);
    const data = await response.text();
    return data;
  } catch (error) {
    console.log(error.message);
    return null;
  }
}

function itemsPorPantalla() {
  let itemsPorPantalla = 30;

  const altoPantalla = screen.height;

  if (altoPantalla <= 768) itemsPorPantalla = 18;

  return itemsPorPantalla;
}

function crearBotonesPaginacion(totalPages, currentPage, maxVisible) {
  const container = document.getElementById("paginador");

  container.innerHTML = "";

  const prevBtn = document.createElement("button");
  prevBtn.textContent = "◀ Anterior";
  prevBtn.className = "first-page";

  container.appendChild(prevBtn);

  for (let i = currentPage; i <= maxVisible; i++) {
    const pageBtn = document.createElement("button");
    pageBtn.textContent = i;
    pageBtn.className = "page-btn";

    container.appendChild(pageBtn);
  }

  // botón siguiente
  const nextBtn = document.createElement("button");
  nextBtn.textContent = "Siguiente ▶";
  nextBtn.className = "next-page";
  if (currentPage === totalPages) nextBtn.disabled = true;
  nextBtn.addEventListener("click", () => {
    if (currentPage < totalPages) {
      currentPage++;
    }
  });
  container.appendChild(nextBtn);
}

function cleanActives() {
  document.querySelectorAll('.page-btn').forEach(btn => btn.classList.remove('active'));
}
