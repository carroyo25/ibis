$(() => {
    $("#esperar").fadeOut();

    $("#btnConsult").click(function(e){
        try {
            $("#esperar").css({"display":"block","opacity":"1"});

            if ( $("#tipoSearch").val() === "-1" ) throw new Error("Seleccione el tipo de Evaluación");

            let cabecera = "";

            if ( $("#tipoSearch").val() === "1"){

                cabecera = `<tr>
                        <th rowspan="3"class="filter">Orden</th>  
                        <th rowspan="3">Emision</th>
                        <th rowspan="3" width="15%" class="filter">Descripción</th>
                        <th rowspan="3" class="filter">Centro Costos</th> 
                        <th rowspan="3" width="15%" class="filter">Proveedor</th>
                        <th colspan="5">ALMACÉN RECEPCIÓN</th>
                        <th colspan="7">QA/QC</th> 
                        <th colspan="5">ALMACEN OBRA</th>
                        <th colspan="8">COMPRAS</th> 
                        <th rowspan="3" class="filter">Total</th>
                    </tr>
                    <tr>
                        <th>Fecha de Entrega</th>
                        <th>Condiciones de Llegada</th>
                        <th>Embalaje de Proveedor</th>
                        <th>Cantidad Entregada</th>
                        <th>Documentación</th>

                        <th>Cumplimiento Técnico</th>
                        <th>Documentación</th>
                        <th>Inspeccion Visual</th>
                        <th>Trazabilidad</th>
                        <th>Comunicación</th>
                        <th>Aceptación de Reclamos</th>
                        <th>Almacenamiento y Preservacion</th>

                        <th>Cantidad Entregada</th>
                        <th>Condiciones de Llegada</th>
                        <th>Embalaje</th>
                        <th>Garantía del Material</th>
                        <th>Documentación</th>

                        <th>Precio Competitivo</th>
                        <th>Descuento</th>
                        <th>Delivery</th>
                        <th>Aceptación de Reclamos</th>
                        <th>Forma de Pago</th>
                        <th>Comunicación</th>
                        <th>Seriedad</th>
                        <th>Capacitación</th>
                    </tr>`;
            }else {
                cabecera = `<tr>
                                <th rowspan="3" class="filter">Orden</th>  
                                <th rowspan="3">Emision</th>
                                <th rowspan="3" width="15%" class="filter">Descripción</th>
                                <th rowspan="3" class="filter">Centro Costos</th> 
                                <th rowspan="3" width="15%" class="filter">Proveedor</th>
                                <th colspan="8">QA/QC</th> 
                                <th colspan="7">COMPRAS</th> 
                                <th colspan="3">USUARIO / GERENTE DE PROYECTO</th>
                                <th rowspan="3" class="filter">Total</th>
                            </tr>
                            <tr>
                                <th>Plan de Gestion</th>
                                <th>Plan de Puntos</th>
                                <th>Procedimientos</th>
                                <th>Requisitos</th>
                                <th>Aceptación de Reclamos</th>
                                <th>Comunicación</th>
                                <th>Dossier</th>
                                <th>Avisa oportunamente</th>
                                
                                <th>Precio</th>
                                <th>Descuento</th>
                                <th>Notificaciones</th>
                                <th>Aceptación de Reclamos</th>
                                <th>Forma de pago</th>
                                <th>Comunicación</th>
                                <th>Seriedad</th>
                                
                                <th>Fecha de Entrega</th>
                                <th>Calidad</th>
                                <th>Cantidad de Procesos</th>
                                
                            </tr>`;
            }

            $.post(RUTA+"evalrepo/evaluaciones", $("#formConsulta").serialize(),
                        function (data, text, requestXHR) {
                            $("#esperar").css({"display":"none","opacity":"0"}).promise().done(function() {
                                
                                $("#cargoPlanDescrip thead").empty().append(cabecera);
                                $("#cargoPlanDescrip tbody").empty().append(data);

                                iniciarPaginador();
                            });
                        },
                        "text"
                    );
            
            
            
        } catch (error) {
            mostrarMensaje(error,"mensaje_error");
        }
    });

    $("#btnExporta").click(function(e){
        e.preventDefault();

        $("#esperar").css({"display":"block","opacity":"1"});

       
        $.post(RUTA+"evalrepo/evaluacionesExcel", $("#formConsulta").serialize(),
            function (data, textStatus, jqXHR) {
                window.location.href = data.documento;
                $("#esperar").css({"display":"none","opacity":"0"});
            },
            "json"
        );

        return false;
    });
});

function iniciarPaginador() {
    const content = document.querySelector('.itemsCargoPlanner'); 
    let itemsPerPage = 100; // Valor por defecto
    let currentPage = 0;
    const maxVisiblePages = 10; // Número máximo de botones visibles
    const items = Array.from(content.getElementsByTagName('tr')).slice(2); // Tomar todos los <tr>, excepto el primero (encabezado)

    // Mostrar una página específica
    function showPage(page) {
        const startIndex = page * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        items.forEach((item, index) => {
            item.classList.toggle('hidden', index < startIndex || index >= endIndex);
        });
        updateActiveButtonStates();
        createPageButtons();
    }

    // Crear los botones de paginación y el selector de elementos por página
    function createPageButtons() {
        const totalPages = Math.ceil(items.length / itemsPerPage);
        let paginationContainer = document.querySelector('.pagination');

        // Si el contenedor de paginación no existe, crearlo
        if (!paginationContainer) {
            paginationContainer = document.createElement('div');
            paginationContainer.classList.add('pagination');
            content.appendChild(paginationContainer);
        } else {
            // Limpiar el contenedor existente
            paginationContainer.innerHTML = '';
        }

        // Crear el selector para elementos por página
        const itemsPerPageSelect = document.createElement('select');
        const options = [25, 50, 100, 150, 200, 250, 300];

        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option;
            opt.textContent = option;
            if (option === itemsPerPage) opt.selected = true; // Establecer 100 como seleccionado por defecto
            itemsPerPageSelect.appendChild(opt);
        });

        // Agregar evento al selector
        itemsPerPageSelect.addEventListener("change", function() {
            itemsPerPage = parseInt(this.value); // Actualizar el número de elementos por página
            currentPage = 0; // Reiniciar a la primera página
            createPageButtons();
            showPage(currentPage);
        });

        paginationContainer.appendChild(itemsPerPageSelect); // Agregar el selector al contenedor de paginación

        // Botón "Primera"
        const firstButton = document.createElement('button');
        firstButton.textContent = 'Primera';
        firstButton.disabled = currentPage === 0;
        firstButton.addEventListener('click', () => {
            currentPage = 0;
            showPage(currentPage);
        });
        paginationContainer.appendChild(firstButton);

        // Botón "Anterior"
        const prevButton = document.createElement('button');
        prevButton.textContent = 'Anterior';
        prevButton.disabled = currentPage === 0;
        prevButton.addEventListener('click', () => {
            if (currentPage > 0) {
                currentPage--;
                showPage(currentPage);
            }
        });
        paginationContainer.appendChild(prevButton);

        // Mostrar botones limitados
        const startPage = Math.max(0, currentPage - Math.floor(maxVisiblePages / 2));
        const endPage = Math.min(totalPages, startPage + maxVisiblePages);

        for (let i = startPage; i < endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i + 1;
            pageButton.disabled = i === currentPage; // Deshabilitar botón si es la página actual
            pageButton.classList.toggle('active', i === currentPage); // Agregar la clase 'active' si es la página actual
            pageButton.addEventListener('click', () => {
                currentPage = i;
                showPage(currentPage);
            });

            paginationContainer.appendChild(pageButton);
        }

        // Botón "Siguiente"
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Siguiente';
        nextButton.disabled = currentPage === totalPages - 1;
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages - 1) {
                currentPage++;
                showPage(currentPage);
            }
        });
        paginationContainer.appendChild(nextButton);

        // Botón "Última"
        const lastButton = document.createElement('button');
        lastButton.textContent = 'Última';
        lastButton.disabled = currentPage === totalPages - 1;
        lastButton.addEventListener('click', () => {
            currentPage = totalPages - 1;
            showPage(currentPage);
        });
        paginationContainer.appendChild(lastButton);
    }

    // Actualizar los estados activos de los botones de paginación
    function updateActiveButtonStates() {
        const pageButtons = document.querySelectorAll('.pagination button');
        pageButtons.forEach((button, index) => {
            // Remover clase 'active' de todos los botones
            button.classList.remove('active');
            // Si el botón es el de la página actual, agregar la clase 'active'
            if (parseInt(button.textContent) === currentPage + 1) {
                button.classList.add('active');
            }
        });
    }

    // Inicializar la paginación
    createPageButtons();
    showPage(currentPage); // Mostrar la primera página
}

