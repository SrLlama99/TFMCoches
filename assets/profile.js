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

        if (btnAdelante) {
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
        }

        if (btnAtras) {
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
        }
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
                    // Update profile image if backend returned the permanent URL
                    const profileImg = document.querySelector('.profile-card-custom img');
                    if (data.profilePicUrl && profileImg) {
                        profileImg.src = data.profilePicUrl;
                    } else if (data.profilePic && profileImg) {
                        profileImg.src = '/assets/images/' + data.profilePic;
                    }
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

    // Live preview of profile image when selecting a file
    const garajeInput = document.getElementById('garajeImage');
    if (garajeInput) {
        garajeInput.addEventListener('change', (event) => {
            const file = event.target.files && event.target.files[0];
            if (!file) return;
            const url = URL.createObjectURL(file);
            const profileImg = document.querySelector('.profile-card-custom img');
            if (profileImg) {
                // set a temporary blob URL for live preview
                profileImg.dataset._blob = url;
                profileImg.src = url;
                profileImg.onload = () => {
                    URL.revokeObjectURL(url);
                };
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

    // Delete comment 
    let currentDeleteCommentTarget = null;
    const confirmCommentModalEl = document.getElementById('confirmDeleteCommentModal');
    const confirmCommentYes = document.getElementById('confirm-delete-comment-yes');
    const confirmCommentNo = document.getElementById('confirm-delete-comment-no');

    if (confirmCommentNo && !confirmCommentNo.dataset.handlerAttached) {
        confirmCommentNo.addEventListener('click', (e) => { e.preventDefault(); });
        confirmCommentNo.dataset.handlerAttached = '1';
    }

    if (confirmCommentYes && !confirmCommentYes.dataset.handlerAttached) {
        confirmCommentYes.addEventListener('click', async (e) => {
            e.preventDefault();
            const deleteUrl = confirmCommentYes.dataset.deleteUrl;
            console.debug('[profile.js] confirm delete clicked, url=', deleteUrl);
            if (!deleteUrl || deleteUrl === '#') return;
            confirmCommentYes.classList.add('disabled');
            try {
                const resp = await fetch(deleteUrl, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await resp.json();
                console.debug('[profile.js] delete response:', json);
                if (json.success) {
                    if (currentDeleteCommentTarget && currentDeleteCommentTarget.parentNode) currentDeleteCommentTarget.parentNode.removeChild(currentDeleteCommentTarget);
                    const modalInst = bootstrap.Modal.getInstance(confirmCommentModalEl) || new bootstrap.Modal(confirmCommentModalEl);
                    modalInst.hide();
                } else {
                    alert(json.error || 'Error deleting comment');
                }
            } catch (err) {
                console.error(err);
                alert('Error deleting comment');
            } finally {
                confirmCommentYes.classList.remove('disabled');
            }
        });
        confirmCommentYes.dataset.handlerAttached = '1';
    }

    document.addEventListener('click', (ev) => {
        let node = ev.target; let btn = null;
        while (node && node !== document) {
            if (node.nodeType === 1 && node.classList && node.classList.contains('delete-comment-btn')) { btn = node; break; }
            node = node.parentNode;
        }
        if (!btn) return;
        ev.preventDefault();
        ev.stopPropagation();
        const deleteUrl = btn.getAttribute('data-delete-url') || btn.dataset.deleteUrl;
        currentDeleteCommentTarget = btn.closest ? btn.closest('.comment-item') : (function(){ let n=btn; while(n && n.classList && !n.classList.contains('comment-item')) n = n.parentNode; return n; })();
        if (confirmCommentYes) {
            confirmCommentYes.dataset.deleteUrl = deleteUrl || '';
            console.debug('[profile.js] delete button clicked, set confirm url=', confirmCommentYes.dataset.deleteUrl);
        }
        if (confirmCommentModalEl) {
            const modal = bootstrap.Modal.getInstance(confirmCommentModalEl) || new bootstrap.Modal(confirmCommentModalEl);
            modal.show();
        }
    });

    // Garage card modal handling
    const garageModalEl = document.getElementById('garageModal');
    if (garageModalEl) {
        const garageModal = new bootstrap.Modal(garageModalEl);

        document.querySelectorAll('.garage-card').forEach(card => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                const cocheId = card.getAttribute('data-coche-id');
                const notas = card.getAttribute('data-notas') || '';
                let photos = [];
                try { photos = JSON.parse(card.getAttribute('data-photos') || '[]'); } catch (err) { photos = []; }

                // populate modal
                const notesEl = document.getElementById('garage-notes');
                if (notesEl) notesEl.textContent = notas;
                const cocheInput = document.getElementById('garage-coche-id');
                if (cocheInput) cocheInput.value = cocheId;

                const photosContainer = document.getElementById('garage-photos');
                if (photosContainer) {
                    photosContainer.innerHTML = '';
                    const canEdit = garageModalEl.dataset.canEdit === '1';
                    photos.forEach(p => {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'position-relative';
                        wrapper.style.width = '160px';
                        wrapper.style.height = '100px';
                        wrapper.style.margin = '4px';

                        const img = document.createElement('img');
                        img.src = '/assets/images/' + (p.url || p);
                        img.style.objectFit = 'cover';
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.className = 'rounded';

                        // append image first
                        wrapper.appendChild(img);
                        if (canEdit) {
                            const del = document.createElement('button');
                            del.className = 'btn-danger position-absolute photo-delete-btn';
                            del.style.top = '6px';
                            del.style.right = '6px';
                            del.style.width = '28px';
                            del.style.height = '28px';
                            del.style.padding = '0';
                            del.style.borderRadius = '50%';
                            del.style.display = 'flex';
                            del.style.alignItems = 'center';
                            del.style.justifyContent = 'center';
                            del.style.fontSize = '0.85rem';
                            del.textContent = '✕';
                            del.dataset.photoId = p.id || '';
                            del.addEventListener('click', async (ev) => {
                                ev.stopPropagation();
                                const pid = del.dataset.photoId;
                                if (!pid) return;
                                try {
                                    const resp = await fetch('/garaje/photo/' + pid + '/delete', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                    const json = await resp.json();
                                    if (json.success) {
                                        wrapper.remove();
                                        // also remove from source card data-photos
                                        try {
                                            const cardPhotos = JSON.parse(card.getAttribute('data-photos') || '[]');
                                            const filtered = cardPhotos.filter(x => String(x.id) !== String(pid));
                                            card.setAttribute('data-photos', JSON.stringify(filtered));
                                        } catch(e) {}
                                    } else {
                                        alert(json.error || 'Delete failed');
                                    }
                                } catch (err) {
                                    console.error(err);
                                    alert('Error deleting');
                                }
                            });
                            wrapper.appendChild(del);
                        }
                        photosContainer.appendChild(wrapper);
                    });
                }

                garageModal.show();
            });
        });

        // upload handler
        const uploadBtn = document.getElementById('garage-upload-btn');
        if (uploadBtn) {
            uploadBtn.addEventListener('click', async () => {
                const cocheId = document.getElementById('garage-coche-id').value;
                const input = document.getElementById('garage-photos-input');
                if (!input || !input.files.length) return alert('Select files to upload');

                const fd = new FormData();
                for (let i = 0; i < input.files.length; i++) fd.append('photos[]', input.files[i]);

                try {
                    const resp = await fetch('/garaje/' + cocheId + '/add-photos', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const json = await resp.json();
                    if (json.success && Array.isArray(json.uploaded)) {
                        const photosContainer = document.getElementById('garage-photos');
                        json.uploaded.forEach(u => {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'position-relative';
                            wrapper.style.width = '160px';
                            wrapper.style.height = '100px';
                            wrapper.style.margin = '4px';

                            const img = document.createElement('img');
                            img.src = u.urlPublic || ('/assets/images/' + u.url);
                            img.style.objectFit = 'cover';
                            img.style.width = '100%';
                            img.style.height = '100%';
                            img.className = 'rounded';

                            // append image first
                            wrapper.appendChild(img);
                            // only add delete button if allowed
                            const canEditNow = garageModalEl.dataset.canEdit === '1';
                            if (canEditNow) {
                                const del = document.createElement('button');
                                del.className = 'btn-danger position-absolute photo-delete-btn';
                                del.style.top = '6px';
                                del.style.right = '6px';
                                del.style.width = '28px';
                                del.style.height = '28px';
                                del.style.padding = '0';
                                del.style.borderRadius = '50%';
                                del.style.display = 'flex';
                                del.style.alignItems = 'center';
                                del.style.justifyContent = 'center';
                                del.style.fontSize = '0.85rem';
                                del.textContent = '✕';
                                del.dataset.photoId = u.id;
                                del.addEventListener('click', async (ev) => {
                                    ev.stopPropagation();
                                    try {
                                            const r2 = await fetch('/garaje/photo/' + u.id + '/delete', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                            const j2 = await r2.json();
                                            if (j2.success) {
                                                wrapper.remove();
                                                try {
                                                    const targetCard = document.querySelector('.garage-card[data-coche-id="' + cocheId + '"]');
                                                    if (targetCard) {
                                                        const current = JSON.parse(targetCard.getAttribute('data-photos') || '[]');
                                                        const filtered = current.filter(x => String(x.id) !== String(u.id));
                                                        targetCard.setAttribute('data-photos', JSON.stringify(filtered));
                                                    }
                                                } catch (e) { console.error(e); }
                                            } else alert(j2.error || 'Delete failed');
                                        } catch (err) { console.error(err); alert('Error deleting'); }
                                });
                                wrapper.appendChild(del);
                            }
                            photosContainer.appendChild(wrapper);
                            // update original card dataset
                            try {
                                const targetCard = document.querySelector('.garage-card[data-coche-id="' + cocheId + '"]');
                                if (targetCard) {
                                    const current = JSON.parse(targetCard.getAttribute('data-photos') || '[]');
                                    current.push({ id: u.id, url: u.url });
                                    targetCard.setAttribute('data-photos', JSON.stringify(current));
                                }
                            } catch (e) { console.error(e); }
                        });
                        // clear input
                        input.value = '';
                    } else {
                        alert(json.error || 'Upload failed');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Error uploading');
                }
            });
        }
    }

});
