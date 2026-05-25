import * as bootstrap from 'bootstrap';

const cfg = (typeof window !== 'undefined' && window.modelPageConfig) ? window.modelPageConfig : {};

document.addEventListener('DOMContentLoaded', () => {
  const profilePathTemplate = cfg.profilePathTemplate || '';
  const isAdmin = cfg.isAdmin ? 1 : 0;
  const valoracionDeleteUrlTemplate = cfg.valoracionDeleteUrlTemplate || '';
  const addRatingUrl = cfg.addRatingUrl || '';
  const commentsUrl = cfg.commentsUrl || '';

  const makeDeleteBtn = (id) => {
    if (!isAdmin) return '';
    return `<button class="delete-comment-btn" data-delete-url="${valoracionDeleteUrlTemplate.replace('__ID__', id)}">✕</button>`;
  };

  const getAvatarSrc = (item) => {
    if (item && item.avatar) return item.avatar;
    const name = (item && item.username) ? String(item.username).trim() : '';
    const initial = name ? name.charAt(0).toUpperCase() : '';
    return `https://via.placeholder.com/45?text=${encodeURIComponent(initial)}`;
  };

  let commentOffset = Number(cfg.initialCommentOffset || 0);
  const loadMoreBtn = document.getElementById('load-more-comments');
  const wrapper = document.getElementById('comments-wrapper');

  // Handle delete-model-link 
  const deleteLinks = document.querySelectorAll('.delete-model-link');
  if (deleteLinks && deleteLinks.length) {
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
            // set dataset so our generic handler can use it
            yesBtn.dataset.deleteUrl = deleteUrl;
            yesBtn.setAttribute('href', deleteUrl);
          } else {
            yesBtn.setAttribute('href', '#');
            yesBtn.onclick = function(ev) {
              ev.preventDefault();
              const modalInst = bootstrap.Modal.getInstance(confirmModalEl) || new bootstrap.Modal(confirmModalEl);
              modalInst.hide();
            };
            yesBtn.dataset.deleteUrl = '';
          }
        }

        const modalInst = bootstrap.Modal.getInstance(confirmModalEl) || new bootstrap.Modal(confirmModalEl);
        modalInst.show();
      });
    });
  }

  const parseServerDateToUnix = (dateStr) => {
    if (!dateStr) return null;
    if (/^\d+$/.test(String(dateStr).trim())) return parseInt(dateStr, 10);
    const m = String(dateStr).trim().match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2}):(\d{2})$/);
    if (m) {
      const year = parseInt(m[1],10), month = parseInt(m[2],10)-1, day = parseInt(m[3],10);
      const hour = parseInt(m[4],10), minute = parseInt(m[5],10), second = parseInt(m[6],10);
      return Math.floor(Date.UTC(year, month, day, hour, minute, second) / 1000);
    }
    const d = new Date(dateStr);
    if (!isNaN(d.getTime())) return Math.floor(d.getTime() / 1000);
    return null;
  };

  const formatTimeAgo = (timestamp) => {
    const now = Math.floor(Date.now() / 1000);
    const diff = now - timestamp;
    if (diff < 0) return 'right now';
    if (diff < 60) return `${diff} seconds ago`;
    const min = Math.floor(diff / 60); if (min < 60) return `${min} minutes ago`;
    const hours = Math.floor(diff / 3600); if (hours < 24) return `${hours} hours ago`;
    const days = Math.floor(diff / 86400); if (days < 7) return `${days} days ago`;
    const weeks = Math.floor(days / 7); if (weeks < 5) return `${weeks} weeks ago`;
    const months = Math.floor(days / 30); if (months < 12) return `${months} months ago`;
    const years = Math.floor(days / 365); return `${years} years ago`;
  };

  // initialize existing time-ago nodes
  document.querySelectorAll('.time-ago').forEach(el => {
    let timestamp = parseInt(el.getAttribute('data-timestamp'), 10);
    if (!timestamp || isNaN(timestamp)) {
      const raw = el.getAttribute('data-timestamp');
      const parsed = parseServerDateToUnix(raw);
      timestamp = parsed;
    }
    if (timestamp && !isNaN(timestamp)) el.textContent = formatTimeAgo(timestamp);
    else el.textContent = '';
  });

  // Toggle owner's notes visibility
  const anadirGarajeCheckbox = document.getElementById('anadirGaraje');
  const notasContainer = document.getElementById('notas-propietario-container');
  if (anadirGarajeCheckbox && notasContainer) {
    const setNotasVisibility = () => { notasContainer.style.display = anadirGarajeCheckbox.checked ? 'block' : 'none'; };
    anadirGarajeCheckbox.addEventListener('change', setNotasVisibility);
    setNotasVisibility();
  }

  // Rating form AJAX
  const ratingForm = document.getElementById('ajax-rating-form');
  if (ratingForm && addRatingUrl) {
    ratingForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById('submit-form-btn');
      submitBtn.disabled = true; submitBtn.innerText = 'Sending...';

      const formData = new FormData(ratingForm);
      fetch(addRatingUrl, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            const modalEl = document.getElementById('ratingModal');
            if (typeof bootstrap !== 'undefined') {
              const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
              modalInstance.hide();
            }
            const item = json.data;
            const userHref = profilePathTemplate.replace('__USERNAME__', encodeURIComponent(item.username));
            const _ts = parseServerDateToUnix(item.fecha) || Math.floor(Date.now()/1000);
            const timeAgo = item.timeAgo || formatTimeAgo(_ts);
            const html = `
              <div class="comment-item">
                <img src="${getAvatarSrc(item)}" class="comment-avatar">
                <div class="comment-content">
                  <h6 class="mb-0 fw-bold"><a href="${userHref}" class="username-link">${item.username}</a> <small class="text-white small ms-2 time-ago" data-timestamp="${_ts || ''}">${timeAgo}</small></h6>
                  <p class="text-white small mb-1">${item.comentario}</p>
                  <div class="rating-static">
                    ${[1,2,3,4,5].map(i => `<svg class="${i <= item.estrellas ? 'gold' : ''}" viewbox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>`).join('')}
                  </div>
                  <div class="small text-white mt-1">
                    ${item.motor ? `<span class="me-2 text-white"><strong>Engine:</strong> ${item.motor}</span>` : ''}
                    ${item.color ? `<span class="me-2 text-white"><strong>Color:</strong> ${item.color}</span>` : ''}
                    ${item.anio ? `<span class="me-2 text-white"><strong>Year:</strong> ${item.anio}</span>` : ''}
                    ${item.transmision ? `<span class="me-2 text-white"><strong>Transmission:</strong> ${item.transmision}</span>` : ''}
                  </div>
                </div>
                ${makeDeleteBtn(item.id || '')}
              </div>`;
            if (wrapper) wrapper.insertAdjacentHTML('afterbegin', html);
            ratingForm.reset();
            const fileInput = document.getElementById('garajeImage'); if (fileInput) fileInput.value = '';
          } else {
            alert(json.error || 'Error procesando valoración');
          }
        })
        .catch(err => { console.error(err); alert('Error enviando la valoración'); })
        .finally(() => { const submitBtn = document.getElementById('submit-form-btn'); if (submitBtn) { submitBtn.disabled = false; submitBtn.innerText = 'Post review'; } });
    });
  }

  // Delete comment delegation + modal helpers
  let currentDeleteTarget = null;
  const confirmModalEl = document.getElementById('confirmDeleteModal');
  const confirmYes = document.getElementById('confirm-delete-yes');

  function showModal(el) {
    if (!el) return;
    if (typeof bootstrap !== 'undefined') { const modal = new bootstrap.Modal(el); modal.show(); return; }
    el.classList.add('show'); el.style.display = 'block'; el.removeAttribute('aria-hidden'); el.setAttribute('aria-modal','true'); el.setAttribute('role','dialog');
    const backdrop = document.createElement('div'); backdrop.className = 'modal-backdrop fade show'; backdrop.dataset._isBack = '1'; document.body.appendChild(backdrop); document.body.classList.add('modal-open');
  }
  function hideModal(el) {
    if (!el) return;
    if (typeof bootstrap !== 'undefined') { const inst = bootstrap.Modal.getInstance(el); if (inst) inst.hide(); else { const m = new bootstrap.Modal(el); m.hide(); } return; }
    el.classList.remove('show'); el.style.display = 'none'; el.setAttribute('aria-hidden','true'); el.removeAttribute('aria-modal'); el.removeAttribute('role');
    const back = document.querySelector('.modal-backdrop[data-_is-back]') || document.querySelector('.modal-backdrop'); if (back && back.parentNode) back.parentNode.removeChild(back); document.body.classList.remove('modal-open');
  }

  if (confirmYes && !confirmYes.dataset.handlerAttached) {
    confirmYes.addEventListener('click', (e) => {
      e.preventDefault(); const deleteUrl = confirmYes.dataset.deleteUrl; if (!deleteUrl) return; confirmYes.classList.add('disabled');
      fetch(deleteUrl, { method:'POST', headers:{ 'X-Requested-With':'XMLHttpRequest' } }).then(r=>r.json()).then(j => {
        if (j.success) { if (currentDeleteTarget && currentDeleteTarget.parentNode) currentDeleteTarget.parentNode.removeChild(currentDeleteTarget); hideModal(confirmModalEl); }
        else alert(j.error || 'Error deleting comment');
      }).catch(err => { console.error(err); alert('Error deleting comment'); }).finally(()=>{ confirmYes.classList.remove('disabled'); });
    });
    confirmYes.dataset.handlerAttached = '1';
  }

  const confirmNo = document.getElementById('confirm-delete-no');
  if (confirmNo && !confirmNo.dataset.handlerAttached) { confirmNo.addEventListener('click', (e)=>{ e.preventDefault(); hideModal(confirmModalEl); }); confirmNo.dataset.handlerAttached = '1'; }

  if (confirmModalEl) {
    const closeBtn = confirmModalEl.querySelector('.btn-close'); if (closeBtn && !closeBtn.dataset.handlerAttached) { closeBtn.addEventListener('click', (e)=>{ e.preventDefault(); hideModal(confirmModalEl); }); closeBtn.dataset.handlerAttached = '1'; }
  }

  document.addEventListener('click', (ev) => {
    let node = ev.target; let btn = null;
    while (node && node !== document) { if (node.nodeType === 1 && node.classList && node.classList.contains('delete-comment-btn')) { btn = node; break; } node = node.parentNode; }
    if (!btn) return; ev.preventDefault(); const deleteUrl = btn.getAttribute('data-delete-url');
    currentDeleteTarget = btn.closest ? btn.closest('.comment-item') : (function(){ let n=btn; while(n && n.classList && !n.classList.contains('comment-item')) n = n.parentNode; return n; })();
    const bodyP = confirmModalEl ? confirmModalEl.querySelector('.modal-body p') : null; if (bodyP) bodyP.textContent = 'Are you sure you want to delete this comment?';
    if (confirmYes) confirmYes.dataset.deleteUrl = deleteUrl; if (confirmModalEl) showModal(confirmModalEl);
  });

  // Load more comments
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
      loadMoreBtn.innerText = 'Loading...';
      fetch(`${commentsUrl}?offset=${commentOffset}&limit=10`).then(res=>res.json()).then(json=>{
        const items = (json.data||[]);
        if (!items.length) { loadMoreBtn.innerText = 'There are no more comments'; loadMoreBtn.disabled = true; return; }
        items.forEach(item => {
          const _ts = parseServerDateToUnix(item.fecha);
          const timeAgo = item.timeAgo || (_ts ? formatTimeAgo(_ts) : '');
          const userHref = profilePathTemplate.replace('__USERNAME__', encodeURIComponent(item.username));
          const html = `
            <div class="comment-item" style="opacity:0; transform:translateY(10px); transition:all .3s">
              <img src="${getAvatarSrc(item)}" class="comment-avatar">
              <div class="comment-content">
                <h6 class="mb-0 fw-bold"><a href="${userHref}" class="username-link">${item.username}</a> <small class="text-white small ms-2 time-ago" data-timestamp="${_ts || ''}">${timeAgo}</small></h6>
                <p class="text-white small mb-1">${item.comentario}</p>
                <div class="rating-static">
                  ${[1,2,3,4,5].map(i => `<svg class="${i <= item.estrellas ? 'gold' : ''}" viewbox="0 0 24 24"><path d=\"M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z\"></path></svg>`).join('')}
                </div>
                <div class="small text-white mt-1">
                  ${item.motor ? `<span class="me-2"><strong>Engine:</strong> ${item.motor}</span>` : ''}
                  ${item.color ? `<span class="me-2"><strong>Color:</strong> ${item.color}</span>` : ''}
                  ${item.anio ? `<span class="me-2"><strong>Year:</strong> ${item.anio}</span>` : ''}
                  ${item.transmision ? `<span class="me-2"><strong>Transmission:</strong> ${item.transmision}</span>` : ''}
                </div>
              </div>
              ${makeDeleteBtn(item.id || '')}
            </div>`;
          if (wrapper) wrapper.insertAdjacentHTML('beforeend', html);
          const el = wrapper && wrapper.lastElementChild;
          if (el) setTimeout(()=>{ el.style.opacity='1'; el.style.transform='translateY(0)'; },50);
        });
        commentOffset += items.length; loadMoreBtn.innerText = 'Load more comments';
        if (items.length < 10) { loadMoreBtn.innerText = 'There are no more comments'; loadMoreBtn.disabled = true; }
      }).catch(err => { console.error(err); loadMoreBtn.innerText = 'Error cargando'; });
    });
  }
});
