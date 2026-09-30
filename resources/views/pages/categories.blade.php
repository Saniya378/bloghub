@extends('layouts.app')

@section('title', 'Categories — Browse All Topics on BlogHub')
@section('meta_description', 'Explore all content categories on BlogHub. Browse essays, tech deep dives, and cultural commentary by topic.')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="mb-5">
            <span class="text-primary fw-semibold">Topics & Disciplines</span>
            <h1 class="h2 fw-bold">Browse by Category</h1>
            <p class="text-muted">Explore curated fields of inquiry spanning technology, culture, science, design, and more.</p>
        </div>

        <div class="row g-4">
            @foreach($categories as $category => $count)
            <div class="col-md-6 col-lg-4">
                <a href="{{ url('/blogs?category=' . $category) }}" class="text-decoration-none">
                    <div class="card h-100 p-4 d-flex align-items-center">
                        <i class="fa-solid {{ $categoryIcons[$category] ?? 'fa-folder' }} fa-2x text-primary me-3"></i>
                        <div>
                            <h3 class="h5 fw-semibold text-dark mb-1">{{ ucfirst($category) }}</h3>
                            <p class="text-muted small mb-2">{{ $categoryDescriptions[$category] ?? '' }}</p>
                            <span class="badge bg-light text-dark">{{ $count }} articles &rarr;</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
