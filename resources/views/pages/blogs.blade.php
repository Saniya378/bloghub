@extends('layouts.app')

@section('title', 'Blogs Ã¢â‚¬â€ Explore All Articles on BlogHub')
@section('meta_description', 'Search, filter, and browse all long-form essays, tech deep dives, and cultural commentary published on BlogHub.')

@section('content')
<section class="py-5">
    <div class="container">
        <!-- Header -->
        <div class="mb-5">
            <span class="text-primary fw-semibold">The Archive</span>
            <h1 class="h2 fw-bold">Explore All Articles</h1>
            <p class="text-muted">Search, filter, and sort through every essay, deep dive, and cultural reflection published on BlogHub.</p>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search articles..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <!-- Category -->
                    <div class="col-md-3">
                        <select class="form-select" id="categorySelect">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Author -->
                    <div class="col-md-3">
                        <select class="form-select" id="authorSelect">
                            <option value="">All Authors</option>
                            @foreach(\App\Models\User::where('role', 'author')->get() as $author)
                            <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Sort -->
                    <div class="col-md-2">
                        <select class="form-select" id="sortSelect">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="text-muted" id="resultCount">Showing {{ $posts->firstItem() ?? 0 }}-{{ $posts->lastItem() ?? 0 }} of {{ $posts->total() }}</div>
            <div class="btn-group">
                <button class="btn btn-outline-primary active" id="gridViewBtn"><i class="fa-solid fa-th-large"></i></button>
                <button class="btn btn-outline-primary" id="listViewBtn"><i class="fa-solid fa-list"></i></button>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div class="text-center py-5 d-none" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Posts Grid (data-live-feed makes new author posts appear live) -->
        <div class="row g-4"
             id="postsContainer"
             data-live-feed
             data-live-cursor="{{ $posts->max('id') ?? 0 }}"
             data-live-interval="20000">
            @foreach($posts as $post)
                @include('components.article-card-cell', ['article' => $post])
            @endforeach
        </div>

        <!-- Empty State -->
        <div class="text-center py-5 d-none" id="emptyState">
            <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
            <h3 class="h5">No articles found</h3>
            <p class="text-muted">Try adjusting your search or filters.</p>
            <button class="btn btn-primary" id="clearFiltersBtn">Clear Filters</button>
        </div>

        <!-- Pagination -->
        <nav class="mt-5" id="paginationNav">
            @if($posts->hasPages())
            <ul class="pagination justify-content-center">
                @if($posts->onFirstPage())
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
                @else
                <li class="page-item"><a class="page-link" href="{{ $posts->previousPageUrl() }}" data-page="{{ $posts->currentPage() - 1 }}">Previous</a></li>
                @endif

                @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $posts->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}" data-page="{{ $page }}">{{ $page }}</a>
                </li>
                @endforeach

                @if($posts->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $posts->nextPageUrl() }}" data-page="{{ $posts->currentPage() + 1 }}">Next</a></li>
                @else
                <li class="page-item disabled"><span class="page-link">Next</span></li>
                @endif
            </ul>
            @endif
        </nav>
    </div>
</section>
@endsection
@push('scripts')
{{-- New posts from authors, appended without a reload --}}
<script src="{{ asset('assets/js/live-feed.js') }}" defer></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const authorSelect = document.getElementById('authorSelect');
    const sortSelect = document.getElementById('sortSelect');
    const postsContainer = document.getElementById('postsContainer');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const emptyState = document.getElementById('emptyState');
    const resultCount = document.getElementById('resultCount');
    const paginationNav = document.getElementById('paginationNav');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');

    let currentPage = 1;
    let isGridView = true;
    let searchTimeout;

    // Initialize from URL params
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('category')) categorySelect.value = urlParams.get('category');
    if (urlParams.get('author')) authorSelect.value = urlParams.get('author');
    if (urlParams.get('sort')) sortSelect.value = urlParams.get('sort');
    if (urlParams.get('search')) searchInput.value = urlParams.get('search');
    currentPage = parseInt(urlParams.get('page')) || 1;

    function fetchPosts() {
        loadingSpinner.classList.remove('d-none');
        postsContainer.classList.add('d-none');
        emptyState.classList.add('d-none');
        paginationNav.classList.add('d-none');

        const params = new URLSearchParams({
            search: searchInput.value,
            category: categorySelect.value,
            author: authorSelect.value,
            sort: sortSelect.value,
            page: currentPage
        });

        fetch(`/api/posts?${params}`)
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.add('d-none');

                if (data.data.length === 0) {
                    emptyState.classList.remove('d-none');
                    resultCount.textContent = 'No results found';
                } else {
                    postsContainer.classList.remove('d-none');
                    postsContainer.innerHTML = data.html;

                    // Update result count
                    const start = (data.pagination.current_page - 1) * data.pagination.per_page + 1;
                    const end = Math.min(start + data.pagination.per_page - 1, data.pagination.total);
                    resultCount.textContent = `Showing ${start}-${end} of ${data.pagination.total} articles`;

                    // Update pagination
                    renderPagination(data.pagination);
                    paginationNav.classList.remove('d-none');
                }

                // Update URL
                updateUrl(params);
            })
            .catch(error => {
                loadingSpinner.classList.add('d-none');
                postsContainer.classList.remove('d-none');
                resultCount.textContent = 'Error loading posts';
            });
    }

    function renderPagination(pagination) {
        let html = '<ul class="pagination justify-content-center">';

        // Previous
        if (pagination.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a></li>`;
        } else {
            html += '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        // Next
        if (pagination.has_more) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a></li>`;
        } else {
            html += '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }

        html += '</ul>';
        paginationNav.innerHTML = html;
    }

    function updateUrl(params) {
        const newUrl = `${window.location.pathname}?${params}`;
        window.history.pushState({ path: newUrl }, '', newUrl);
    }

    // Event listeners
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            fetchPosts();
        }, 300);
    });

    categorySelect.addEventListener('change', () => { currentPage = 1; fetchPosts(); });
    authorSelect.addEventListener('change', () => { currentPage = 1; fetchPosts(); });
    sortSelect.addEventListener('change', () => { currentPage = 1; fetchPosts(); });

    // Pagination clicks
    paginationNav.addEventListener('click', (e) => {
        e.preventDefault();
        const page = e.target.closest('.page-link')?.dataset.page;
        if (page) {
            currentPage = parseInt(page);
            fetchPosts();
        }
    });

    // View toggle
    gridViewBtn.addEventListener('click', () => {
        isGridView = true;
        gridViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
        postsContainer.querySelectorAll('.col-md-6').forEach(col => {
            col.classList.remove('col-lg-12');
            col.classList.add('col-lg-4');
        });
    });

    listViewBtn.addEventListener('click', () => {
        isGridView = false;
        listViewBtn.classList.add('active');
        gridViewBtn.classList.remove('active');
        postsContainer.querySelectorAll('.col-md-6').forEach(col => {
            col.classList.remove('col-lg-4');
            col.classList.add('col-lg-12');
        });
    });

    // Clear filters
    clearFiltersBtn.addEventListener('click', () => {
        searchInput.value = '';
        categorySelect.value = '';
        authorSelect.value = '';
        sortSelect.value = 'newest';
        currentPage = 1;
        fetchPosts();
    });

    // Handle browser back/forward
    window.addEventListener('popstate', () => {
        const params = new URLSearchParams(window.location.search);
        searchInput.value = params.get('search') || '';
        categorySelect.value = params.get('category') || '';
        authorSelect.value = params.get('author') || '';
        sortSelect.value = params.get('sort') || 'newest';
        currentPage = parseInt(params.get('page')) || 1;
        fetchPosts();
    });

    // Initial fetch
    fetchPosts();
});
</script>
@endpush
