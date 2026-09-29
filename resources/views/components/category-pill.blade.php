@props(['category', 'count'])

<a href="{{ url('/blogs?category=' . $category) }}" class="text-decoration-none">
    <div class="category-pill d-flex align-items-center p-3 rounded-3 border">
        <i class="fa-solid {{ $categoryIcons[$category] ?? 'fa-folder' }} fa-lg me-3 text-primary"></i>
        <div>
            <div class="fw-semibold text-dark">{{ ucfirst($category) }}</div>
            <small class="text-muted">{{ $count }} articles</small>
        </div>
    </div>
</a>
