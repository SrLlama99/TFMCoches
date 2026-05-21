import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const deleteLinks = document.querySelectorAll('.delete-model-link');
    if (!deleteLinks.length) return;

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
                yesBtn.onclick = null;
                if (deleteUrl && deleteUrl !== '#') {
                    yesBtn.setAttribute('href', deleteUrl);
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
});
