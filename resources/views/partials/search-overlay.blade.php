<!-- Search Overlay -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h2 class="h5 modal-title" id="searchModalLabel">Search Articles</h2>
                <div class="input-group">
                    <label for="searchInput" class="sr-only">Search articles</label>
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search articles..." autocomplete="off">
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close search dialog"></button>
            </div>
            <div class="modal-body">
                <div id="searchSuggestions" class="list-group list-group-flush" role="listbox" aria-label="Search suggestions">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const suggestionsContainer = document.getElementById('searchSuggestions');
    const searchModal = document.getElementById('searchModal');
    let debounceTimer;

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const query = searchInput.value.trim();

            if (query.length < 2) {
                suggestionsContainer.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.suggestions.length === 0) {
                            suggestionsContainer.innerHTML = '<div class="list-group-item text-muted">No results found</div>';
                            return;
                        }
                        suggestionsContainer.innerHTML = data.suggestions.map(s => `
                            <a href="${s.url}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>${s.title}</span>
                                    <span class="badge bg-light text-dark">${s.category}</span>
                                </div>
                            </a>
                        `).join('');
                    });
            }, 300);
        });

        // Enter key goes to search page
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && searchInput.value.trim()) {
                window.location.href = `/search?q=${encodeURIComponent(searchInput.value.trim())}`;
            }
        });
    }
});
</script>
