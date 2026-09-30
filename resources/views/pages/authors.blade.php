@extends('layouts.app')

@section('title', 'Authors — Meet the Writers of BlogHub')
@section('meta_description', 'Browse all contributing authors on BlogHub. Discover writers, their specialties, and published essays.')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="mb-5">
            <span class="text-primary fw-semibold">Contributors</span>
            <h1 class="h2 fw-bold">Our Authors</h1>
            <p class="text-muted">Meet the researchers, essayists, and practitioners who publish on BlogHub.</p>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                <input type="text" class="form-control" id="authorSearch" placeholder="Search by name or specialty..." value="{{ $query }}">
            </div>
        </div>

        <div class="row g-4" id="authorsGrid">
            @foreach($authors as $author)
            <div class="col-md-6 col-lg-4 author-card" data-name="{{ strtolower($author->name) }}" data-specialty="{{ strtolower($author->specialty) }}">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="{{ $author->avatar }}" alt="{{ $author->name }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        <h3 class="h5 fw-semibold mb-1">{{ $author->name }}</h3>
                        <p class="text-primary small mb-2">{{ $author->specialty }}</p>
                        <p class="text-muted small mb-3">{{ $author->tagline }}</p>
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <span class="text-muted"><strong>{{ $author->articles_count }}</strong> articles</span>
                        </div>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ url('/authors/' . $author->username) }}" class="btn btn-outline-primary btn-sm">View Profile</a>
                            <button class="btn btn-primary btn-sm follow-btn" data-author-id="{{ $author->id }}">
                                <i class="fa-solid fa-plus me-1"></i> Follow
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center py-5 d-none" id="noAuthors">
            <i class="fa-solid fa-users fa-3x text-muted mb-3"></i>
            <h3 class="h5">No authors found</h3>
            <p class="text-muted">Try a different search term.</p>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('authorSearch');
    const authorCards = document.querySelectorAll('.author-card');
    const noAuthors = document.getElementById('noAuthors');
    let debounceTimer;

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = searchInput.value.toLowerCase();
            let visibleCount = 0;

            authorCards.forEach(card => {
                const name = card.dataset.name;
                const specialty = card.dataset.specialty;
                const match = name.includes(query) || specialty.includes(query);
                card.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            noAuthors.classList.toggle('d-none', visibleCount > 0);
        }, 300);
    });

    // Follow buttons
    document.querySelectorAll('.follow-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const authorId = btn.dataset.authorId;
            try {
                const response = await fetch(`/authors/${authorId}/follow`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                if (data.success) {
                    btn.innerHTML = data.following ? '<i class="fa-solid fa-check me-1"></i> Following' : '<i class="fa-solid fa-plus me-1"></i> Follow';
                    btn.classList.toggle('btn-primary', !data.following);
                    btn.classList.toggle('btn-outline-primary', data.following);
                }
            } catch (error) {
                showToast('Error', 'Failed to follow author.', 'fa-solid fa-exclamation-circle text-danger');
            }
        });
    });
});
</script>
@endpush
