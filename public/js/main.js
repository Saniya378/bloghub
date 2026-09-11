// ============================================
// BlogHub — Shared JS (Section 09 requirements)
// ============================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- Current year in footer ----
  var yearEl = document.getElementById('currentYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  // ---- Dark mode toggle (persisted in localStorage) ----
  var themeBtn = document.getElementById('themeToggleBtn');
  var htmlEl = document.documentElement;
  var savedTheme = localStorage.getItem('bloghub-theme') || 'light';
  htmlEl.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);

  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      var current = htmlEl.getAttribute('data-theme');
      var next = current === 'dark' ? 'light' : 'dark';
      htmlEl.setAttribute('data-theme', next);
      localStorage.setItem('bloghub-theme', next);
      updateThemeIcon(next);
    });
  }

  function updateThemeIcon(theme) {
    if (!themeBtn) return;
    var icon = themeBtn.querySelector('i');
    if (icon) icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
  }

  // ---- Scroll progress bar ----
  var progressBar = document.getElementById('scroll-progress');
  window.addEventListener('scroll', function () {
    var scrollTop = window.scrollY;
    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
    var pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    if (progressBar) progressBar.style.width = pct + '%';

    // Sticky navbar shadow
    var navbar = document.getElementById('siteNavbar');
    if (navbar) {
      if (scrollTop > 12) navbar.classList.add('scrolled');
      else navbar.classList.remove('scrolled');
    }

    // Back to top visibility
    var backToTop = document.getElementById('back-to-top');
    if (backToTop) {
      if (scrollTop > 400) backToTop.classList.add('show');
      else backToTop.classList.remove('show');
    }
  });

  // ---- Back to top click ----
  var backToTopBtn = document.getElementById('back-to-top');
  if (backToTopBtn) {
    backToTopBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ---- Scroll reveal animation ----
  var revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach(function (el) { observer.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('visible'); });
  }

  // ---- Counter animation ----
  var counters = document.querySelectorAll('.counter');
  if ('IntersectionObserver' in window && counters.length) {
    var counterObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          counterObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    counters.forEach(function (el) { counterObserver.observe(el); });
  }

  function animateCounter(el) {
    var target = parseInt(el.getAttribute('data-target'), 10) || 0;
    var duration = 1500;
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var value = Math.floor(progress * target);
      el.textContent = value.toLocaleString();
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = target.toLocaleString() + (el.getAttribute('data-suffix') || '');
      }
    }
    requestAnimationFrame(step);
  }

  // ---- Newsletter form (fake client-side submit) ----
  var newsletterForm = document.getElementById('footerNewsletterForm');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var input = newsletterForm.querySelector('input[type="email"]');
      if (input && input.value.includes('@')) {
        showToast('Subscribed! Check your inbox for a welcome note.', 'success');
        input.value = '';
      } else {
        showToast('Please enter a valid email address.', 'error');
      }
    });
  }

  // ---- Generic toast helper (window-scoped so any page can call it) ----
  window.showToast = function (message, type) {
    var container = document.getElementById('toastContainer');
    if (!container) return;
    var bg = type === 'error' ? 'bg-danger' : type === 'success' ? 'bg-success' : 'bg-dark';
    var toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-white ' + bg + ' border-0';
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML =
      '<div class="d-flex">' +
        '<div class="toast-body">' + message + '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
      '</div>';
    container.appendChild(toastEl);
    var bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
    bsToast.show();
    toastEl.addEventListener('hidden.bs.toast', function () { toastEl.remove(); });
  };

});
