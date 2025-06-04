let verSoloHoy = false; // Flag to show only today's bids

/**
 * Display spinner while loading results.
 */
const mostrarSpinner = () => {
    const resultadosDiv = document.getElementById('licitaciones-resultados');
    if (resultadosDiv) {
        resultadosDiv.innerHTML = `
            <div class="spinner">
                <div class="spinner-icon"></div>
                <p>Aguarde un momento...</p>
            </div>
        `;
    }
};

/**
 * Load bids via AJAX.
 * @param {Object} data - Parameters sent to the AJAX handler.
 */
const cargarLicitaciones = (data = {}) => {
    data.action = 'filtrar_licitaciones';
    if (!data.paged) data.paged = 1;
    if (!('orden' in data)) {
        const sel = document.getElementById('orden');
        data.orden = sel ? sel.value : '';
    }
    if (!('ver_solo_hoy' in data)) data.ver_solo_hoy = verSoloHoy ? '1' : '0';

    mostrarSpinner();

    fetch(licitacionesAjax.ajax_url, {
        method: 'POST',
        body: new URLSearchParams(data),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
    .then(response => response.json())
    .then(result => {
        const resultadosDiv = document.getElementById('licitaciones-resultados');
        const paginacionDiv = document.getElementById('paginacion');
        if (resultadosDiv) resultadosDiv.innerHTML = '';
        if (paginacionDiv) paginacionDiv.innerHTML = '';

        if (result.success === false) {
            if (resultadosDiv) resultadosDiv.innerHTML = `<p>${result.data}</p>`;
            return;
        }

        if (result.licitaciones.length === 0) {
            if (resultadosDiv) resultadosDiv.innerHTML = '<p>No se encontraron licitaciones.</p>';
            return;
        }

        // Render each bid card
        result.licitaciones.forEach(licitacion => {
            const licitacionHTML = `
                <div class="licitacion-card">
                    <h3 class="licitacion-titulo">${licitacion.titulo}</h3>
                    <p><strong>Fecha de apertura:</strong> ${licitacion.fecha_apertura}</p>
                    <p><strong>Tipo de obra:</strong> ${licitacion.tipo_de_obra}</p>
                    <p><strong>Lugar:</strong> ${licitacion.lugar}</p>
                    <p><strong>Comitente:</strong> ${licitacion.comitente}</p>
                    <a class="btn-ver-mas" href="${licitacion.url}" target="_blank">Ver más</a>
                </div>
            `;
            if (resultadosDiv) resultadosDiv.innerHTML += licitacionHTML;
        });

        // Pagination
        const totalPages = result.total_pages;
        const currentPage = result.current_page;

        if (totalPages > 1 && paginacionDiv) {
            const createPageLink = (page, label, isActive = false) => {
                const link = document.createElement('a');
                link.href = '#';
                link.textContent = label;
                link.className = isActive ? 'active' : '';
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    data.paged = page;
                    cargarLicitaciones(data);
                });
                return link;
            };

            if (currentPage > 1) {
                paginacionDiv.appendChild(createPageLink(1, '1', currentPage === 1));
            }

            if (currentPage > 3) {
                const dots = document.createElement('span');
                dots.textContent = '...';
                paginacionDiv.appendChild(dots);
            }

            for (let i = Math.max(1, currentPage - 1); i <= Math.min(totalPages, currentPage + 1); i++) {
                paginacionDiv.appendChild(createPageLink(i, i, currentPage === i));
            }

            if (currentPage < totalPages - 2) {
                const dots = document.createElement('span');
                dots.textContent = '...';
                paginacionDiv.appendChild(dots);
            }

            if (currentPage < totalPages) {
                paginacionDiv.appendChild(createPageLink(totalPages, totalPages));
            }
        }
    });
};

// Add event listeners safely
document.addEventListener('DOMContentLoaded', () => {
    const ordenSel = document.getElementById('orden');
    if (ordenSel) {
        ordenSel.addEventListener('change', function() {
            const data = {
                action: 'filtrar_licitaciones',
                paged: 1,
                buscador: document.getElementById('buscador') ? document.getElementById('buscador').value : '',
                fecha_desde: document.getElementById('fecha_desde') ? document.getElementById('fecha_desde').value : '',
                fecha_hasta: document.getElementById('fecha_hasta') ? document.getElementById('fecha_hasta').value : '',
                tipo_de_obra: document.getElementById('tipo_de_obra') ? document.getElementById('tipo_de_obra').value : '',
                lugar: document.getElementById('lugar') ? document.getElementById('lugar').value : '',
                orden: this.value,
                ver_solo_hoy: verSoloHoy ? '1' : '0',
            };
            cargarLicitaciones(data);
        });
    }

    const toggleBtn = document.getElementById('toggle-licitaciones-dia');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            verSoloHoy = !verSoloHoy;
            this.textContent = verSoloHoy ? 'Ver todas las licitaciones' : 'Ver sólo licitaciones del día';

            const data = {
                action: 'filtrar_licitaciones',
                paged: 1,
                buscador: document.getElementById('buscador') ? document.getElementById('buscador').value : '',
                fecha_desde: document.getElementById('fecha_desde') ? document.getElementById('fecha_desde').value : '',
                fecha_hasta: document.getElementById('fecha_hasta') ? document.getElementById('fecha_hasta').value : '',
                tipo_de_obra: document.getElementById('tipo_de_obra') ? document.getElementById('tipo_de_obra').value : '',
                lugar: document.getElementById('lugar') ? document.getElementById('lugar').value : '',
                orden: document.getElementById('orden') ? document.getElementById('orden').value : '',
                ver_solo_hoy: verSoloHoy ? '1' : '0',
            };
            cargarLicitaciones(data);
        });
    }

    const clearBtn = document.getElementById('limpiar-filtros');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            const form = document.getElementById('licitaciones-buscador');
            if (form) form.reset();
            const orden = document.getElementById('orden');
            if (orden) orden.value = '';
            verSoloHoy = false;
            if (toggleBtn) toggleBtn.textContent = 'Ver sólo licitaciones del día';
            cargarLicitaciones();
        });
    }

    const form = document.getElementById('licitaciones-buscador');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const data = {
                action: 'filtrar_licitaciones',
                buscador: document.getElementById('buscador') ? document.getElementById('buscador').value : '',
                fecha_desde: document.getElementById('fecha_desde') ? document.getElementById('fecha_desde').value : '',
                fecha_hasta: document.getElementById('fecha_hasta') ? document.getElementById('fecha_hasta').value : '',
                tipo_de_obra: document.getElementById('tipo_de_obra') ? document.getElementById('tipo_de_obra').value : '',
                lugar: document.getElementById('lugar') ? document.getElementById('lugar').value : '',
                orden: document.getElementById('orden') ? document.getElementById('orden').value : '',
                ver_solo_hoy: verSoloHoy ? '1' : '0',
                paged: 1,
            };
            cargarLicitaciones(data);
        });
    }

    // Initial load
    cargarLicitaciones();
});
