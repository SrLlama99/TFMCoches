import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const formatTimeAgo = (timestamp) => {
        const now = Math.floor(Date.now() / 1000);
        const diff = now - timestamp;

        if (diff < 0) return 'right now';
        if (diff < 60) return `${diff} seconds ago`;

        const min = Math.floor(diff / 60);
        if (min < 60) return `${min} minutes ago`;

        const hours = Math.floor(diff / 3600);
        if (hours < 24) return `${hours} hours ago`;

        const days = Math.floor(diff / 86400);
        if (days < 7) return `${days} days ago`;

        const weeks = Math.floor(days / 7);
        if (weeks < 5) return `${weeks} weeks ago`;

        const months = Math.floor(days / 30);
        if (months < 12) return `${months} months ago`;

        const years = Math.floor(days / 365);
        return `${years} years ago`;
    };

    document.querySelectorAll('.time-ago').forEach(el => {
        const timestamp = parseInt(el.getAttribute('data-timestamp'), 10);
        el.textContent = formatTimeAgo(timestamp);
    });

    const galerias = document.querySelectorAll('.gallery-wrapper');
    galerias.forEach(galeria => {
        const contenedor = galeria.querySelector('.scroll-contenedor');
        const btnAdelante = galeria.querySelector('.btn-adelante');
        const btnAtras = galeria.querySelector('.btn-atras');

        if (!contenedor) return;

        btnAdelante.addEventListener('click', () => {
            const item = contenedor.querySelector('.custom-item');
            if (!item) return;

            const itemWidth = item.offsetWidth;
            contenedor.scrollBy({ left: itemWidth, behavior: 'smooth' });
            setTimeout(() => {
                const primero = contenedor.firstElementChild;
                contenedor.appendChild(primero);
                contenedor.classList.add('no-smooth');
                contenedor.scrollLeft -= itemWidth;
                contenedor.classList.remove('no-smooth');
            }, 500);
        });

        btnAtras.addEventListener('click', () => {
            const item = contenedor.querySelector('.custom-item');
            if (!item) return;

            const itemWidth = item.offsetWidth;
            const ultimo = contenedor.lastElementChild;
            contenedor.classList.add('no-smooth');
            contenedor.prepend(ultimo);
            contenedor.scrollLeft += itemWidth;
            contenedor.classList.remove('no-smooth');
            setTimeout(() => {
                contenedor.scrollBy({ left: -itemWidth, behavior: 'smooth' });
            }, 10);
        });
    });

    // Edit profile form submit via AJAX
    const editForm = document.getElementById('ajax-editProfile-form');
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(editForm);
            const url = editForm.dataset.updateUrl;

            try {
                const resp = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await resp.json();
                if (data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }

                    const h3 = document.querySelector('.profile-card-custom h3');
                    if (h3 && data.username) h3.textContent = data.username;
                    const modalEl = document.getElementById('editProfileModal');
                    if (modalEl) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modalInstance.hide();
                    }
                } else {
                    alert(data.error || 'Update failed');
                }
            } catch (err) {
                console.error(err);
                alert('Error updating profile');
            }
        });
    }

    // Delete account confirmation modal handling
    const deleteLinks = document.querySelectorAll('.delete-account-link');
    if (deleteLinks.length) {
        deleteLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const deleteUrl = link.dataset.deleteUrl || link.getAttribute('href');

                // hide any open modal first
                const openModalEl = document.querySelector('.modal.show');
                if (openModalEl) {
                    const openModal = bootstrap.Modal.getInstance(openModalEl) || new bootstrap.Modal(openModalEl);
                    openModal.hide();
                }

                const confirmModalEl = document.getElementById('confirmDeleteModal');
                if (!confirmModalEl) return;

                const yesBtn = document.getElementById('confirm-delete-yes');
                if (yesBtn) {
                    // reset previous handlers
                    yesBtn.onclick = null;
                    if (deleteUrl && deleteUrl !== '#') {
                        yesBtn.setAttribute('href', deleteUrl);
                        // let the link navigate when clicked
                    } else {
                        yesBtn.setAttribute('href', '#');
                        yesBtn.onclick = function(ev) {
                            ev.preventDefault();
                            const modalInst = bootstrap.Modal.getInstance(confirmModalEl) || new bootstrap.Modal(confirmModalEl);
                            modalInst.hide();
                        };
                    }
                }

                const modalInst = bootstrap.Modal.getInstance(confirmModalEl) || new bootstrap.Modal(confirmModalEl);
                modalInst.show();
            });
        });
    }

});
