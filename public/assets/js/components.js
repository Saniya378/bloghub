/**
 * BlogHub — Reusable Component Renderers (components.js)
 * Defines the core `renderBlogCard` function reused across all pages,
 * along with author, category, and trending component generators.
 */

/**
 * Universal Reusable Blog Card Renderer
 * Reused across Home, Blogs Listing, Author Profile, Related Posts, and Search Results.
 *
 * @param {Object} article - Article object from data.js
 * @param {Object} options - Customization flags:
 *   - layout: 'grid' (default) | 'list' | 'featured-hero' | 'featured-compact'
 *   - showActions: boolean (show like / bookmark buttons)
 *   - showFullExcerpt: boolean (show complete vs trimmed excerpt)
 * @returns {string} Semantic HTML5 <article> markup
 */
function renderBlogCard(article, options = {}) {
  if (!article) return '';

  const layout = options.layout || 'grid';
  const showActions = options.showActions !== false;
  const showStats = options.showStats === true;
  
  // Resolve Author from data.js
  let author = { name: 'Staff Contributor', slug: 'staff', photo: 'https://i.pravatar.cc/100?img=1' };
  if (window.blogHubData && Array.isArray(window.blogHubData.authors)) {
    const found = window.blogHubData.authors.find(a => a.id === article.authorId);
    if (found) author = found;
  }

  const articleUrl = `/blog-details?slug=${encodeURIComponent(article.slug)}`;
  const authorUrl = `/author-profile?slug=${encodeURIComponent(author.slug || 'author')}`;
  const categoryUrl = `/blogs?category=${encodeURIComponent(article.categorySlug || 'technology')}`;

  // 1. Featured Hero Layout (Used in Asymmetric Grid)
  if (layout === 'featured-hero') {
    return `
      <article class="bh-card bh-card-hover h-100" data-article-id="${article.id}">
        <div class="bh-card-img-wrap" style="aspect-ratio: 16 / 9; max-height: 380px;">
          <img src="${article.image}" alt="${article.title}" width="800" height="450" loading="lazy" decoding="async">
        </div>
        <div class="bh-card-body p-4 p-lg-5">
          <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <a href="${categoryUrl}" class="bh-badge bh-badge-accent">${article.category}</a>
            <span class="bh-caption"><i class="fa-regular fa-clock me-1"></i>${article.readingTime}</span>
          </div>
          <h3 class="bh-card-title mb-3" style="font-size: 1.85rem; line-height: 1.3;">
            <a href="${articleUrl}">${article.title}</a>
          </h3>
          <p class="bh-card-text mb-4" style="font-size: 1.0625rem; line-height: 1.6;">
            ${article.excerpt}
          </p>
          <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 mt-auto gap-3" style="border-top: 1px solid var(--color-border-subtle);">
            <a href="${authorUrl}" class="d-flex align-items-center gap-2 text-decoration-none" style="color: var(--color-text);">
              <img src="${author.photo}" alt="${author.name}" width="40" height="40" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid var(--color-border);" loading="lazy" decoding="async">
              <div>
                <strong class="d-block" style="font-size: 0.9375rem; color: var(--color-primary);">${author.name}</strong>
                <span class="bh-caption">${article.date}</span>
              </div>
            </a>
            <div class="d-flex align-items-center gap-2">
              <a href="${articleUrl}" class="btn-bh btn-bh-primary">
                Read Story &rarr;
              </a>
              ${showActions ? `
                <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon bh-btn-bookmark" data-id="${article.id}" aria-label="Bookmark article ${article.title}" title="Save article">
                  <i class="fa-regular fa-bookmark"></i>
                </button>
              ` : ''}
            </div>
          </div>
        </div>
      </article>
    `;
  }

  // 2. Featured Compact Layout (Right column of asymmetric grid)
  if (layout === 'featured-compact') {
    return `
      <article class="bh-card bh-card-hover h-100" data-article-id="${article.id}">
        <div class="row g-0 h-100">
          <div class="col-sm-5 bh-card-img-wrap" style="aspect-ratio: auto; min-height: 180px;">
            <img src="${article.image}" alt="${article.title}" width="400" height="300" loading="lazy" decoding="async" style="height: 100%; width: 100%; object-fit: cover;">
          </div>
          <div class="col-sm-7 p-3 p-md-4 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center justify-content-between mb-2">
                <a href="${categoryUrl}" class="bh-badge bh-badge-secondary">${article.category}</a>
                <span class="bh-caption">${article.readingTime}</span>
              </div>
              <h3 class="bh-card-title" style="font-size: 1.15rem; line-height: 1.35;">
                <a href="${articleUrl}">${article.title}</a>
              </h3>
              <p class="bh-card-text mb-2" style="font-size: 0.875rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                ${article.excerpt}
              </p>
            </div>
            <div class="d-flex align-items-center justify-content-between pt-2 mt-2" style="border-top: 1px solid var(--color-border-subtle);">
              <span class="bh-caption">${article.date}</span>
              <a href="${articleUrl}" class="btn-bh btn-bh-ghost" style="padding: 0 var(--space-3); height: 36px; font-size: 0.8125rem;">
                Read &rarr;
              </a>
            </div>
          </div>
        </div>
      </article>
    `;
  }

  // 3. Horizontal List Layout (Used in Blogs Page List Toggle)
  if (layout === 'list') {
    return `
      <article class="bh-card bh-card-hover mb-4" data-article-id="${article.id}">
        <div class="row g-0 align-items-center">
          <div class="col-md-4 bh-card-img-wrap" style="min-height: 220px; max-height: 250px;">
            <img src="${article.image}" alt="${article.title}" width="400" height="250" loading="lazy" decoding="async">
          </div>
          <div class="col-md-8 p-4 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center justify-content-between mb-2">
                <a href="${categoryUrl}" class="bh-badge bh-badge-accent">${article.category}</a>
                <span class="bh-caption"><i class="fa-regular fa-clock me-1"></i>${article.readingTime}</span>
              </div>
              <h3 class="bh-card-title mb-2">
                <a href="${articleUrl}">${article.title}</a>
              </h3>
              <p class="bh-card-text mb-3">
                ${article.excerpt}
              </p>
              ${showStats ? `
                <div class="d-flex align-items-center gap-3 mb-3">
                  <span class="bh-caption"><i class="fa-regular fa-eye me-1"></i>${article.views.toLocaleString()}</span>
                  <span class="bh-caption"><i class="fa-regular fa-comment me-1"></i>${article.commentCount}</span>
                </div>
              ` : ''}
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3" style="border-top: 1px solid var(--color-border-subtle);">
              <div class="d-flex align-items-center gap-2">
                <img src="${author.photo}" alt="${author.name}" width="32" height="32" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;" loading="lazy" decoding="async">
                <span class="bh-caption fw-semibold" style="color: var(--color-primary);">${author.name}</span>
                <span class="bh-caption">&bull;</span>
                <span class="bh-caption">${article.date}</span>
              </div>
              <div class="d-flex align-items-center gap-2">
                <a href="${articleUrl}" class="btn-bh btn-bh-ghost" style="height: 38px; padding: 0 var(--space-4);">Read More</a>
                ${showActions ? `
                  <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon bh-btn-like" data-id="${article.id}" aria-label="Like article" style="height: 38px; width: 38px;">
                    <i class="fa-regular fa-heart"></i>
                    <span class="bh-like-count">${article.likes}</span>
                  </button>
                  <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon bh-btn-bookmark" data-id="${article.id}" aria-label="Bookmark article" style="height: 38px; width: 38px;">
                    <i class="fa-regular fa-bookmark"></i>
                  </button>
                ` : ''}
              </div>
            </div>
          </div>
        </div>
      </article>
    `;
  }

  // 4. Default Grid Card Layout (Home Latest Posts, Blog Grid, Author Articles, etc.)
  return `
    <article class="bh-card bh-card-hover h-100 d-flex flex-column" data-article-id="${article.id}">
      <div class="bh-card-img-wrap">
        <img src="${article.image}" alt="${article.title}" width="400" height="250" loading="lazy" decoding="async">
      </div>
      <div class="bh-card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <a href="${categoryUrl}" class="bh-badge bh-badge-accent">${article.category}</a>
          <span class="bh-caption"><i class="fa-regular fa-clock me-1"></i>${article.readingTime}</span>
        </div>
        <h3 class="bh-card-title mb-2">
          <a href="${articleUrl}">${article.title}</a>
        </h3>
        <p class="bh-card-text">
          ${article.excerpt}
        </p>
        ${showStats ? `
          <div class="d-flex align-items-center gap-3 mb-3">
            <span class="bh-caption"><i class="fa-regular fa-eye me-1"></i>${article.views.toLocaleString()}</span>
            <span class="bh-caption"><i class="fa-regular fa-comment me-1"></i>${article.commentCount}</span>
          </div>
        ` : ''}
        <div class="bh-card-meta mb-0 pt-3 mt-auto" style="border-top: 1px solid var(--color-border-subtle);">
          <a href="${authorUrl}" class="d-flex align-items-center gap-2 text-decoration-none" style="color: inherit;">
            <img src="${author.photo}" alt="${author.name}" width="28" height="28" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;" loading="lazy" decoding="async">
            <span class="fw-semibold" style="color: var(--color-primary);">${author.name}</span>
          </a>
          <span class="ms-auto">${article.date}</span>
        </div>
      </div>
      <div class="bh-card-footer">
        <a href="${articleUrl}" class="btn-bh btn-bh-ghost" style="padding: 0 var(--space-3); height: 36px; font-size: 0.875rem;">
          Read More &rarr;
        </a>
        <div class="d-flex gap-1 align-items-center">
          ${showActions ? `
            <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon bh-btn-like" data-id="${article.id}" style="width: 36px; height: 36px;" aria-label="Like ${article.title}" title="Like">
              <i class="fa-regular fa-heart"></i>
              <span class="bh-like-count">${article.likes}</span>
            </button>
            <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon bh-btn-bookmark" data-id="${article.id}" style="width: 36px; height: 36px;" aria-label="Bookmark ${article.title}" title="Save">
              <i class="fa-regular fa-bookmark"></i>
            </button>
          ` : ''}
        </div>
      </div>
    </article>
  `;
}

/**
 * Reusable Trending Item Renderer (Numbered Compact List)
 * @param {Object} article
 * @param {number} rank - 1-based index
 * @returns {string} HTML markup
 */
function renderTrendingItem(article, rank = 1) {
  if (!article) return '';
  const formattedRank = String(rank).padStart(2, '0');
  const articleUrl = `/blog-details?slug=${encodeURIComponent(article.slug)}`;
  const categoryUrl = `/blogs?category=${encodeURIComponent(article.categorySlug)}`;

  let authorName = 'Staff Writer';
  let authorPhoto = 'https://i.pravatar.cc/60?img=1';
  if (window.blogHubData && Array.isArray(window.blogHubData.authors)) {
    const found = window.blogHubData.authors.find(a => a.id === article.authorId);
    if (found) {
      authorName = found.name;
      authorPhoto = found.photo;
    }
  }

  return `
    <div class="col-12 col-lg-6">
      <div class="bh-trending-item">
        <span class="bh-trending-rank">${formattedRank}</span>
        <div class="bh-trending-content">
          <div class="d-flex align-items-center gap-2 mb-2">
            <img src="${authorPhoto}" alt="${authorName}" width="24" height="24" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;" loading="lazy" decoding="async">
            <span class="bh-caption fw-semibold" style="color: var(--color-primary);">${authorName}</span>
            <span class="bh-caption">&bull;</span>
            <a href="${categoryUrl}" class="bh-caption text-uppercase fw-semibold" style="color: var(--color-accent); text-decoration: none;">${article.category}</a>
          </div>
          <h3 class="bh-trending-title">
            <a href="${articleUrl}" style="color: inherit; text-decoration: none;">${article.title}</a>
          </h3>
          <div class="bh-trending-meta">
            <span>${article.date}</span>
            <span>&bull;</span>
            <span>${article.readingTime}</span>
            <span>&bull;</span>
            <span><i class="fa-regular fa-heart me-1"></i>${article.likes}</span>
          </div>
        </div>
      </div>
    </div>
  `;
}

/**
 * Reusable Category Pill / Card Renderer
 * @param {Object} category
 * @returns {string} HTML markup
 */
function renderCategoryPill(category) {
  if (!category) return '';
  const url = `/blogs?category=${encodeURIComponent(category.slug)}`;

  return `
    <div class="col-6 col-md-4 col-lg-2-4 col-xl-2">
      <a href="${url}" class="bh-category-pill" title="Browse ${category.name} stories">
        <div class="bh-category-icon-box">
          <i class="${category.icon}"></i>
        </div>
        <div>
          <div class="bh-category-title">${category.name}</div>
          <span class="bh-category-count">${category.articleCount} Articles</span>
        </div>
      </a>
    </div>
  `;
}

/**
 * Reusable Author Card Renderer
 * @param {Object} author
 * @returns {string} HTML markup
 */
function renderAuthorCard(author) {
  if (!author) return '';
  const profileUrl = `/author-profile?slug=${encodeURIComponent(author.slug)}`;

  return `
    <div class="col-12 col-md-6 col-lg-4">
      <article class="bh-author-card">
        <a href="${profileUrl}">
          <img src="${author.photo}" alt="Portrait of ${author.name}" width="120" height="120" class="bh-author-avatar" loading="lazy" decoding="async">
        </a>
        <h3 class="bh-author-name">
          <a href="${profileUrl}">${author.name}</a>
        </h3>
        <p class="bh-author-tagline">${author.tagline}</p>
        <div class="bh-author-meta">
          <span class="fw-semibold" style="color: var(--color-primary);">${author.articleCount} Articles</span>
          <span>&bull;</span>
          <span>${author.followers.toLocaleString()} Followers</span>
        </div>
        <div class="mt-auto d-flex gap-2 w-100">
          <a href="${profileUrl}" class="btn-bh btn-bh-ghost flex-grow-1" style="height: 38px;">
            Profile
          </a>
          <button type="button" class="btn-bh btn-bh-accent bh-btn-follow" data-author-id="${author.id}" style="height: 38px; padding: 0 var(--space-4);" aria-label="Follow ${author.name}">
            <i class="fa-solid fa-plus me-1"></i> Follow
          </button>
        </div>
      </article>
    </div>
  `;
}

// Make functions accessible globally
window.renderBlogCard = renderBlogCard;
window.renderTrendingItem = renderTrendingItem;
window.renderCategoryPill = renderCategoryPill;
window.renderAuthorCard = renderAuthorCard;
