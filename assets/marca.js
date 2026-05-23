// marca.js - JS para la página de marca

document.addEventListener('DOMContentLoaded', () => {
    // Carousel gallery handlers (can be multiple galleries)
    const galerias = document.querySelectorAll('.gallery-wrapper');
    galerias.forEach(galeria => {
        const contenedor = galeria.querySelector('.scroll-contenedor');
        const btnAdelante = galeria.querySelector('.btn-adelante');
        const btnAtras = galeria.querySelector('.btn-atras');
        if (!contenedor) return;

        if (btnAdelante) btnAdelante.addEventListener('click', () => {
            const item = contenedor.querySelector('.custom-item');
            if (!item) return;
            const itemWidth = item.offsetWidth;
            contenedor.scrollBy({ left: itemWidth, behavior: 'smooth' });
            setTimeout(() => {
                const primero = contenedor.firstElementChild;
                if (primero) contenedor.appendChild(primero);
                contenedor.classList.add('no-smooth');
                contenedor.scrollLeft -= itemWidth;
                contenedor.classList.remove('no-smooth');
            }, 500);
        });

        if (btnAtras) btnAtras.addEventListener('click', () => {
            const item = contenedor.querySelector('.custom-item');
            if (!item) return;
            const itemWidth = item.offsetWidth;
            const ultimo = contenedor.lastElementChild;
            if (ultimo) {
                contenedor.classList.add('no-smooth');
                contenedor.prepend(ultimo);
                contenedor.scrollLeft += itemWidth;
                contenedor.classList.remove('no-smooth');
                setTimeout(() => {
                    contenedor.scrollBy({ left: -itemWidth, behavior: 'smooth' });
                }, 10);
            }
        });
    });

    // AJAX submit for Edit Brand modal
    const form = document.getElementById('ajax-editBrand-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submit-edit-brand');
            if (btn) { btn.disabled = true; btn.innerText = 'Saving...'; }
            const url = form.dataset.updateUrl;
            const fd = new FormData(form);
            try {
                const resp = await fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await resp.json();
                if (json.success) {
                    const data = json.data || {};
                    const title = document.querySelector('.profile-card-custom h3');
                    if (title && data.nombre) title.textContent = data.nombre;
                    const anchor = document.querySelector('.profile-card-custom a');
                    if (anchor && data.url) anchor.setAttribute('href', data.url);
                    const img = document.querySelector('.profile-card-custom img');
                    if (img && data.logo) img.setAttribute('src', data.logo);
                    // hide modal
                    const modalEl = document.getElementById('editBrandModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();
                    }
                } else {
                    alert(json.error || 'Error updating brand');
                }
            } catch (err) {
                console.error(err);
                alert('Error updating brand');
            } finally {
                if (btn) { btn.disabled = false; btn.innerText = 'Save changes'; }
            }
        });
    }
});
