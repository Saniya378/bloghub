@extends('layouts.app')

@section('title', 'Search Results — BlogHub')
@section('meta_description', 'Search results on BlogHub.')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="mb-5">
            <span class="text-primary fw-semibold">Search</span>
            <h1 class="h2 fw-bold">Results for "{{ $query }}"</h1>
            <p class="text-muted">{{ $results->count() }} article{{ $results->count() !== 1 ? 's' : '' }} found</p>
        </div>

        @if($results->isNotEmpty())
        <div class="row g-4">
            @foreach($results as $article)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <a href="{{ url('/blogs/' . $article->slug) }}" class="text-decoration-none">
                        <img src="{{ $article->image }}" alt="{{ $article->title }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ ucfirst($article->category_slug) }}</span>
                            <small class="text-muted">{{ $article->reading_time }}</small>
                        </div>
                        <h3 class="card-title h6">
                            <a href="{{ url('/blogs/' . $article->slug) }}" class="text-decoration-none text-dark">{{ $article->title }}</a>
                        </h3>
                        <p class="card-text text-muted small flex-grow-1">{{ Str::limit($article->excerpt, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <img src="{{ $article->user->avatar }}" alt="{{ $article->user->name }}" class="rounded-circle me-2" style="width: 28px; height: 28px; object-fit: cover;">
                                <small class="fw-semibold">{{ $article->user->name }}</small>
                            </div>
                            <small class="text-muted">{{ $article->published_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
            <h3 class="h5">No results found</h3>
            <p class="text-muted mb-4">Try browsing these popular categories instead:</p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                @foreach($suggestedCategories as $category)
                <a href="{{ url('/blogs?category=' . $category) }}" class="btn btn-outline-primary">
                    <i class="fa-solid fa-folder me-1"></i> {{ ucfirst($category) }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
