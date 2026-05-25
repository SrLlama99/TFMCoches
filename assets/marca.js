document.addEventListener('DOMContentLoaded', () => {
    // Carousel gallery handlers
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
                let json = null;
                try { json = await resp.json(); } catch (e) { console.error('Invalid JSON response', e); }

                console.debug('marca update response', resp.status, json);

                // Consider success if HTTP status is OK and the server didn't explicitly mark success=false
                const wasSuccessful = resp.ok && !(json && json.success === false);

                if (wasSuccessful) {
                    const data = (json && json.data) ? json.data : {};
                    const title = document.querySelector('.profile-card-custom h3');
                    if (title && data.nombre) title.textContent = data.nombre;
                    const anchor = document.querySelector('.profile-card-custom a');
                    if (anchor && data.url) anchor.setAttribute('href', data.url);
                    const img = document.querySelector('.profile-card-custom img');
                    if (img && data.logo) img.setAttribute('src', data.logo);

                    // hide modal
                    const modalEl = document.getElementById('editBrandModal');
                    if (modalEl) {
                        try {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                                modal.hide();
                            } else if (window.jQuery && typeof jQuery(modalEl).modal === 'function') {
                                jQuery(modalEl).modal('hide');
                            } else {
                                // Fallback: hide modal manually
                                modalEl.classList.remove('show');
                                modalEl.style.display = 'none';
                                document.body.classList.remove('modal-open');
                                const backdrops = document.querySelectorAll('.modal-backdrop');
                                backdrops.forEach(b => b.parentNode && b.parentNode.removeChild(b));
                            }
                        } catch (e) {
                            console.warn('Could not hide modal via bootstrap/jQuery, fallback applied', e);
                            modalEl.classList.remove('show');
                            modalEl.style.display = 'none';
                        }
                    }

                    // If server returned a redirect URL, follow it
                    if (json && json.redirect) {
                        window.location.href = json.redirect;
                        return;
                    }
                } else {
                    alert((json && json.error) ? json.error : 'Error updating brand');
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
