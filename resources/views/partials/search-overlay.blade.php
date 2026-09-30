<!-- Search Overlay Modal -->
<div class="modal fade" id="searchOverlayModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0" style="border-radius: var(--border-radius, 12px); overflow: hidden;">
            <div class="modal-header border-bottom p-3">
                <form action="{{ url('/search') }}" method="GET" class="w-100 me-2" id="searchOverlayForm">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted ps-3">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="q" class="form-control border-start-0 ps-2" id="searchOverlayInput" placeholder="Type to search articles, topics, authors..." autocomplete="off" style="font-size: 1.05rem; height: 48px;">
                        <button class="btn btn-primary px-3" type="submit">Search</button>
                    </div>
                </form>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="searchSuggestionsContainer" style="max-height: 380px; overflow-y: auto;">
                <div id="searchSuggestionsList" class="list-group list-group-flush">
                    <!-- Populated by JS -->
                </div>
                <div id="searchQuickLinks" class="p-3">
                    <div class="small text-muted fw-semibold mb-2">POPULAR TOPICS</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['technology', 'education', 'health', 'lifestyle', 'science', 'business'] as $topic)
                        <a href="{{ url('/blogs?category=' . $topic) }}" class="badge bg-light text-dark text-decoration-none py-2 px-3">
                            {{ ucfirst($topic) }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchModal = document.getElementById('searchOverlayModal');
    const searchInput = document.getElementById('searchOverlayInput');
    const suggestionsList = document.getElementById('searchSuggestionsList');
    const quickLinks = document.getElementById('searchQuickLinks');
    let debounceTimer;

    // Auto-focus input when modal opens
    if (searchModal && searchInput) {
        searchModal.addEventListener('shown.bs.modal', () => {
            searchInput.focus();
        });
    }

    if (searchInput && suggestionsList) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const query = searchInput.value.trim();

            if (query.length < 2) {
                suggestionsList.innerHTML = '';
                if (quickLinks) quickLinks.style.display = 'block';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ url('/api/search/suggestions') }}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (quickLinks) quickLinks.style.display = 'none';

                        if (!data.suggestions || data.suggestions.length === 0) {
                            suggestionsList.innerHTML = `
                                <div class="p-4 text-center text-muted">
                                    <i class="fa-solid fa-file-circle-question fa-2x mb-2 d-block"></i>
                                    No direct matches found. Press <kbd>Enter</kbd> or click <strong>Search</strong> to see full results.
                                </div>
                            `;
                            return;
                        }

                        suggestionsList.innerHTML = data.suggestions.map(s => `
                            <a href="${s.url}" class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark mb-1">${s.title}</div>
                                    <small class="text-muted"><i class="fa-solid fa-tag me-1 text-primary"></i>${s.category ? s.category.toUpperCase() : 'GENERAL'}</small>
                                </div>
                                <i class="fa-solid fa-arrow-right text-muted small"></i>
                            </a>
                        `).join('');
                    })
                    .catch(() => {
                        if (quickLinks) quickLinks.style.display = 'none';
                    });
            }, 250);
        });
    }
});
</script>
