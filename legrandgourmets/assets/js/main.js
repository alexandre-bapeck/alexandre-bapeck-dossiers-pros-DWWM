/* ============================================================
    LE GRAND GOURMET — JavaScript + Animations
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  /* ── PAGE LOADER ─────────────────────────────────────────── */
  const loader = document.getElementById('page-loader');
  if (loader) {
    window.addEventListener('load', () => {
      setTimeout(() => loader.classList.add('hidden'), 600);
    });
    setTimeout(() => loader && loader.classList.add('hidden'), 3000);
  }

  /* ── PROGRESS BAR (scroll) ───────────────────────────────── */
  const progressBar = document.getElementById('progress-bar');
  if (progressBar) {
    window.addEventListener('scroll', () => {
      const scrolled  = window.scrollY;
      const maxScroll = document.body.scrollHeight - window.innerHeight;
      progressBar.style.width = (scrolled / maxScroll * 100) + '%';
    }, { passive: true });
  }

  /* ── NAVBAR : effet au scroll ────────────────────────────── */
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
  }

  /* ── SCROLL TO TOP ───────────────────────────────────────── */
  const scrollTopBtn = document.getElementById('scroll-top');
  if (scrollTopBtn) {
    window.addEventListener('scroll', () => {
      scrollTopBtn.classList.toggle('visible', window.scrollY > 300);
    }, { passive: true });
    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ── BURGER MENU ─────────────────────────────────────────── */
  const toggle = document.getElementById('navToggle');
  const menu   = document.getElementById('navMenu');
  if (toggle && menu) {
    toggle.addEventListener('click', () => menu.classList.toggle('open'));
    document.addEventListener('click', e => {
      if (!toggle.contains(e.target) && !menu.contains(e.target))
        menu.classList.remove('open');
    });
  }

  /* ── NAV DROPDOWN (profil / déconnexion) ─────────────────── */
  const navDropdown = document.querySelector('.nav-dropdown');
  const dropdownToggle = document.querySelector('.nav-dropdown-toggle');
  if (navDropdown && dropdownToggle) {
    // stopPropagation évite que le clic remonte jusqu'au document
    navDropdown.addEventListener('click', e => e.stopPropagation());
    dropdownToggle.addEventListener('click', e => {
      e.preventDefault();
      navDropdown.classList.toggle('open');
    });
    // ferme si on clique n'importe où en dehors
    document.addEventListener('click', () => navDropdown.classList.remove('open'));
  }

  /* ── SCROLL ANIMATIONS (Intersection Observer) ───────────── */
  const animEls = document.querySelectorAll('.anim');
  if (animEls.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    animEls.forEach(el => observer.observe(el));
  }

  /* ── RIPPLE EFFECT sur les boutons ───────────────────────── */
  document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const rect   = btn.getBoundingClientRect();
      const size   = Math.max(rect.width, rect.height);
      const x      = e.clientX - rect.left - size / 2;
      const y      = e.clientY - rect.top  - size / 2;
      const ripple = document.createElement('span');
      ripple.className = 'ripple-effect';
      ripple.style.cssText = `width:${size}px;height:${size}px;left:${x}px;top:${y}px`;
      btn.appendChild(ripple);
      ripple.addEventListener('animationend', () => ripple.remove());
    });
  });

  /* ── TOAST NOTIFICATIONS ─────────────────────────────────── */
  window.showToast = function(msg, type = 'default', duration = 3500) {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      document.body.appendChild(container);
    }
    const icons = { success: '✅', error: '❌', default: '🍽️' };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span>${icons[type] || icons.default}</span><span>${msg}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
      toast.classList.add('hiding');
      toast.addEventListener('animationend', () => toast.remove());
    }, duration);
  };

  /* ── FAVORIS (AJAX + animation) ──────────────────────────── */
  document.querySelectorAll('.btn-favori[data-id]').forEach(btn => {
    btn.addEventListener('click', async e => {
      e.preventDefault();
      e.stopPropagation();
      const id = btn.dataset.id;
      try {
        const res  = await fetch(BASE_URL + '/ajax/favori.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `recette_id=${encodeURIComponent(id)}`
        });
        const data = await res.json();
        if (data.redirect) { window.location = data.redirect; return; }
        if (data.status === 'added') {
          btn.classList.add('active');
          btn.style.animation = 'none';
          requestAnimationFrame(() => { btn.style.animation = 'heartBeat .6s ease'; });
          showToast('Ajouté aux favoris !', 'success');
        } else {
          btn.classList.remove('active');
          showToast('Retiré des favoris', 'default');
        }
      } catch { /* silencieux */ }
    });
  });

  /* ── COMPTEURS ANIMÉS (KPI admin) ────────────────────────── */
  function animateCounter(el) {
    const target = parseInt(el.textContent.replace(/\D/g, ''), 10);
    if (isNaN(target) || target === 0) return;
    let start = 0;
    const duration = 900;
    const startTime = performance.now();
    const tick = (now) => {
      const elapsed  = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const eased    = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(eased * target);
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }
  const kpiObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        kpiObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });
  document.querySelectorAll('.kpi-value').forEach(el => kpiObserver.observe(el));

  /* ── INGRÉDIENTS DYNAMIQUES (admin) ──────────────────────── */
  const ingList   = document.getElementById('ingredients-list');
  const btnAddIng = document.getElementById('btn-add-ing');
  if (ingList && btnAddIng) {
    btnAddIng.addEventListener('click', () => {
      const row = document.createElement('div');
      row.className = 'ing-row';
      row.style.animation = 'fadeInUp .3s ease both';
      row.innerHTML = `
        <input type="text" name="ing_qty[]" class="form-control ing-qty" placeholder="Quantité">
        <input type="text" name="ing_nom[]" class="form-control" placeholder="Ingrédient">
        <button type="button" class="btn btn-sm btn-danger btn-remove-ing">−</button>
      `;
      ingList.appendChild(row);
    });
    ingList.addEventListener('click', e => {
      if (e.target.classList.contains('btn-remove-ing')) {
        const rows = ingList.querySelectorAll('.ing-row');
        if (rows.length > 1) {
          const row = e.target.closest('.ing-row');
          row.style.animation = 'fadeInUp .25s ease reverse both';
          row.addEventListener('animationend', () => row.remove());
        }
      }
    });
  }

  /* ── HOVER IMAGE ZOOM ────────────────────────────────────── */
  document.querySelectorAll('.recipe-card').forEach(card => {
    const img = card.querySelector('.recipe-card-img');
    if (!img) return;
    card.addEventListener('mouseenter', () => img.style.transform = 'scale(1.05)');
    card.addEventListener('mouseleave', () => img.style.transform = 'scale(1)');
  });

  /* ── AUTO-DISMISS ALERT ──────────────────────────────────── */
  document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity .4s, transform .4s';
      el.style.opacity = '0';
      el.style.transform = 'translateY(-8px)';
      setTimeout(() => el.remove(), 400);
    }, 4500);
  });

  /* ── APPARITION EN CASCADE DES RECETTES ─────────────────── */
  document.querySelectorAll('.recipe-card').forEach((card, i) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(24px)';
    card.style.transition = `opacity .45s ease ${i * 0.08}s, transform .45s ease ${i * 0.08}s`;
    requestAnimationFrame(() => requestAnimationFrame(() => {
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }));
  });

  /* ── APPARITION EN CASCADE DES CATÉGORIES ───────────────── */
  document.querySelectorAll('.category-card').forEach((card, i) => {
    card.style.opacity = '0';
    card.style.transform = 'scale(.9)';
    card.style.transition = `opacity .4s ease ${i * 0.07}s, transform .4s ease ${i * 0.07}s`;
    requestAnimationFrame(() => requestAnimationFrame(() => {
      card.style.opacity = '1';
      card.style.transform = 'scale(1)';
    }));
  });

  /* ── TYPING EFFECT sur le placeholder ───────────────────── */
  const searchInput = document.querySelector('.search-input');
  if (searchInput && !searchInput.value) {
    const phrases = ['Rechercher une recette…', 'Spaghetti, tarte, soupe…', 'Un ingrédient, une idée…'];
    let pi = 0, ci = 0, deleting = false;
    const type = () => {
      const current = phrases[pi];
      searchInput.placeholder = deleting ? current.slice(0, ci--) : current.slice(0, ci++);
      if (!deleting && ci === current.length + 1) { deleting = true; setTimeout(type, 1600); return; }
      if (deleting && ci < 0) { deleting = false; pi = (pi + 1) % phrases.length; ci = 0; }
      setTimeout(type, deleting ? 45 : 90);
    };
    setTimeout(type, 1200);
  }

});
