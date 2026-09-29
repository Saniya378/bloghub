/**
 * BlogHub — Global JavaScript (main.js)
 * Features:
 * - Theme Management (Dark Mode toggle with localStorage persistence)
 * - Scroll Progress Bar
 * - Back to Top Button
 * - Sticky Navbar Scroll Elevation
 * - Search Overlay with Live Filtered Suggestions
 * - Toast Notification Helper
 * - Dynamic Footer Copyright Year
 */

// Immediate Theme Initialization to avoid flash of unstyled content (FOUC)
(function initTheme() {
  const savedTheme = localStorage.getItem('bloghub_theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);
})();

document.addEventListener('DOMContentLoaded', () => {
  initThemeToggle();
  initScrollProgress();
  initBackToTop();
  initStickyNavbar();
  initSearchOverlay();
  initFooterYear();
  initScrollReveal();
  initAnimatedCounters();
  initKeyboardNavigation();
});

/**
 * 10. Keyboard Navigation - Escape closes modals/offcanvas
 */
function initKeyboardNavigation() {
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      // Close all modals
      const modals = document.querySelectorAll('.modal.show');
      modals.forEach(modal => {
        const instance = bootstrap.Modal.getInstance(modal);
        if (instance) instance.hide();
      });

      // Close offcanvas
      const offcanvas = document.querySelectorAll('.offcanvas.show');
      offcanvas.forEach(oc => {
        const instance = bootstrap.Offcanvas.getInstance(oc);
        if (instance) instance.hide();
      });

      // Close dropdowns
      const dropdowns = document.querySelectorAll('.dropdown-menu.show');
      dropdowns.forEach(dd => dd.classList.remove('show'));
    }
  });
}

/**
 * 1. Dark Mode Toggle
 * Switches data-theme attribute on <html> and persists choice in localStorage
 */
function initThemeToggle() {
  const toggles = document.querySelectorAll('[data-toggle="theme"]');
  const getTheme = () => document.documentElement.getAttribute('data-theme') || 'light';

  function updateToggleUI() {
    const isDark = getTheme() === 'dark';
    toggles.forEach(btn => {
      const icon = btn.querySelector('i');
      if (icon) {
        icon.className = isDark ? 'fa-solid fa-sun text-warning' : 'fa-solid fa-moon';
      }
      btn.setAttribute('aria-label', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
      btn.setAttribute('title', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
    });
  }

  toggles.forEach(btn => {
    btn.addEventListener('click', () => {
      const current = getTheme();
      const nextTheme = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', nextTheme);
      localStorage.setItem('bloghub_theme', nextTheme);
      updateToggleUI();
      
      if (typeof showToast === 'function') {
        const themeLabel = nextTheme === 'dark' ? 'Dark Mode Activated' : 'Light Mode Activated';
        const themeIcon = nextTheme === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun text-warning';
        showToast(themeLabel, `Theme preference saved.`, themeIcon, 2500);
      }
    });
  });

  updateToggleUI();
}

/**
 * 2. Viewport Scroll Progress Bar
 */
function initScrollProgress() {
  const progressBar = document.querySelector('.bh-scroll-progress-bar');
  if (!progressBar) return;

  const handleScroll = () => {
    const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = height > 0 ? (winScroll / height) * 100 : 0;
    progressBar.style.width = `${Math.min(100, Math.max(0, scrolled))}%`;
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();
}

/**
 * 3. Back to Top Button
 */
function initBackToTop() {
  const btn = document.querySelector('[data-action="back-to-top"]');
  if (!btn) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      btn.classList.add('visible');
    } else {
      btn.classList.remove('visible');
    }
  }, { passive: true });

  btn.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

/**
 * 4. Sticky Navbar Scroll Elevation
 */
function initStickyNavbar() {
  const navbar = document.querySelector('.bh-navbar');
  if (!navbar) return;

  const handleNavScroll = () => {
    if (window.scrollY > 20) {
      navbar.classList.add('bh-navbar-scrolled');
    } else {
      navbar.classList.remove('bh-navbar-scrolled');
    }
  };

  window.addEventListener('scroll', handleNavScroll, { passive: true });
  handleNavScroll();
}

/**
 * 5. Search Overlay Modal with Live Suggestions
 */
function initSearchOverlay() {
  const searchInput = document.getElementById('searchOverlayInput');
  const suggestionsList = document.getElementById('searchSuggestionsList');
  const clearBtn = document.getElementById('clearSearchInputBtn');
  const headerEl = document.getElementById('searchSuggestionsHeader');
  const countEl = document.getElementById('searchResultsCount');
  const modalEl = document.getElementById('searchOverlayModal');

  if (!searchInput || !suggestionsList || !modalEl) return;

  function getSearchIndex() {
    if (window.blogHubData && Array.isArray(window.blogHubData.articles)) {
      const authorMap = (window.blogHubData.authors || []).reduce((acc, a) => {
        acc[a.id] = a.name;
        return acc;
      }, {});

      return window.blogHubData.articles.map(art => ({
        id: art.id,
        slug: art.slug,
        title: art.title,
        category: art.category,
        author: authorMap[art.authorId] || 'Staff Writer',
        url: `/blog-details?slug=${art.slug}`
      }));
    }
    return [];
  }

  function renderSuggestions(query = '') {
    const searchableIndex = getSearchIndex();
    const trimmed = query.trim().toLowerCase();
    suggestionsList.innerHTML = '';

    let matches = [];
    if (!trimmed) {
      headerEl.textContent = 'Popular Searches & Topics';
      countEl.textContent = '';
      if (clearBtn) clearBtn.style.display = 'none';
      matches = searchableIndex.slice(0, 5);
    } else {
      if (clearBtn) clearBtn.style.display = 'inline-flex';
      matches = searchableIndex.filter(item => 
        item.title.toLowerCase().includes(trimmed) ||
        item.category.toLowerCase().includes(trimmed) ||
        item.author.toLowerCase().includes(trimmed)
      );
      headerEl.textContent = matches.length > 0 ? `Results matching "${query}"` : 'No matching results';
      countEl.textContent = `${matches.length} found`;
    }

    if (matches.length === 0) {
      const emptyState = document.createElement('div');
      emptyState.className = 'py-4 text-center';
      emptyState.innerHTML = `
        <i class="fa-solid fa-magnifying-glass mb-2" style="font-size: 1.5rem; color: var(--color-text-light);"></i>
        <p class="bh-body mb-2" style="color: var(--color-text-muted);">No stories matched "${query}".</p>
        <span class="bh-caption">Try searching for "Design", "Technology", or "Elena".</span>
      `;
      suggestionsList.appendChild(emptyState);
      return;
    }

    matches.forEach(item => {
      const a = document.createElement('a');
      a.href = item.url;
      a.className = 'bh-search-suggestion-item';
      
      // Highlight matching term if query exists
      let displayTitle = item.title;
      if (trimmed) {
        const regex = new RegExp(`(${trimmed})`, 'gi');
        displayTitle = displayTitle.replace(regex, '<mark style="background-color: var(--color-accent-subtle); color: var(--color-accent); font-weight: 600; padding: 0 2px;">$1</mark>');
      }

      a.innerHTML = `
        <div class="d-flex flex-column gap-1 me-3">
          <span style="font-weight: 500; font-size: 0.9375rem; color: var(--color-text);">${displayTitle}</span>
          <span class="bh-search-suggestion-meta">By ${item.author} in <strong style="color: var(--color-accent);">${item.category}</strong></span>
        </div>
        <i class="fa-solid fa-arrow-right fa-sm" style="color: var(--color-text-light);"></i>
      `;
      suggestionsList.appendChild(a);
    });
  }

  // Initial render when modal opens
  modalEl.addEventListener('shown.bs.modal', () => {
    searchInput.focus();
    renderSuggestions(searchInput.value);
  });

  searchInput.addEventListener('input', (e) => {
    renderSuggestions(e.target.value);
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      searchInput.value = '';
      searchInput.focus();
      renderSuggestions('');
    });
  }

  // Enter key redirects to search-results
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && searchInput.value.trim()) {
      window.location.href = `/search-results?q=${encodeURIComponent(searchInput.value.trim())}`;
    }
  });
}

/**
 * 6. Dynamic Footer Year via JavaScript
 */
function initFooterYear() {
  const el = document.getElementById('footerCurrentYear');
  if (el) {
    el.textContent = new Date().getFullYear();
  }
}

/**
 * 7. Global Toast Notification Dispatcher
 * @param {string} title
 * @param {string} message
 * @param {string} iconClass (e.g. 'fa-solid fa-circle-check text-success')
 * @param {number} durationMs
 */
function showToast(title, message, iconClass = 'fa-solid fa-circle-info', durationMs = 4000) {
  let container = document.getElementById('toastContainer') || document.querySelector('.bh-toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'bh-toast-container';
    container.setAttribute('aria-live', 'polite');
    container.setAttribute('aria-atomic', 'true');
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = 'bh-toast';
  toast.setAttribute('role', 'alert');

  toast.innerHTML = `
    <div class="bh-toast-icon">
      <i class="${iconClass}"></i>
    </div>
    <div class="bh-toast-body">
      <div class="bh-toast-title">${title}</div>
      <p class="bh-toast-message">${message}</p>
    </div>
    <button type="button" class="bh-toast-close" aria-label="Dismiss notification">
      <i class="fa-solid fa-xmark"></i>
    </button>
  `;

  const closeBtn = toast.querySelector('.bh-toast-close');
  closeBtn.addEventListener('click', () => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(12px)';
    setTimeout(() => toast.remove(), 250);
  });

  container.appendChild(toast);

  setTimeout(() => {
    if (toast.parentElement) {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(12px)';
      setTimeout(() => toast.remove(), 250);
    }
  }, durationMs);
}

// Expose showToast globally
window.showToast = showToast;

/**
 * 8. Scroll Reveal Animations via IntersectionObserver
 */
function initScrollReveal() {
  const elements = document.querySelectorAll('.bh-reveal');
  if (!elements.length) return;

  if (!('IntersectionObserver' in window)) {
    elements.forEach(el => el.classList.add('bh-revealed'));
    return;
  }

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('bh-revealed');
        obs.unobserve(entry.target);
      }
    });
  }, {
    root: null,
    rootMargin: '0px 0px -40px 0px',
    threshold: 0.08
  });

  elements.forEach(el => observer.observe(el));
}

/**
 * 9. Statistics Animated Counters via IntersectionObserver
 */
function initAnimatedCounters() {
  const counters = document.querySelectorAll('[data-counter-target]');
  if (!counters.length) return;

  const animateCounter = (el) => {
    const target = parseInt(el.getAttribute('data-counter-target'), 10) || 0;
    const suffix = el.getAttribute('data-counter-suffix') || '';
    const prefix = el.getAttribute('data-counter-prefix') || '';
    const duration = 1800; // ms
    const startTime = performance.now();

    const update = (now) => {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      // easeOutExpo for natural decelerating count up
      const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
      const current = Math.floor(ease * target);
      el.textContent = `${prefix}${current.toLocaleString()}${suffix}`;

      if (progress < 1) {
        requestAnimationFrame(update);
      } else {
        el.textContent = `${prefix}${target.toLocaleString()}${suffix}`;
      }
    };

    requestAnimationFrame(update);
  };

  if (!('IntersectionObserver' in window)) {
    counters.forEach(el => animateCounter(el));
    return;
  }

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        obs.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.25
  });

  counters.forEach(el => observer.observe(el));
}

window.initScrollReveal = initScrollReveal;
window.initAnimatedCounters = initAnimatedCounters;

