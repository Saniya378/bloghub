@extends('frontend.layouts.app')

@section('title', 'BlogHub - Tag: Artificial Intelligence')

@section('content')

    <section class="container py-5">

        <h2 class="fw-bold mb-1">Tag: Artificial Intelligence</h2>
        <p class="text-secondary mb-5">8 articles found</p>

        <div class="row g-4 mb-4">

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/160x140" class="rounded-start" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">The Future of Artificial Intelligence</h6>
                        <small class="text-secondary">May 16, 2025 &bull; 5 min read</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/160x140" class="rounded-start" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">AI in Healthcare: Benefits and Challenges</h6>
                        <small class="text-secondary">May 15, 2025 &bull; 6 min read</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/160x140" class="rounded-start" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">Machine Learning Basics for Beginners</h6>
                        <small class="text-secondary">May 11, 2025 &bull; 5 min read</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/160x140" class="rounded-start" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">How AI is Changing the World</h6>
                        <small class="text-secondary">May 9, 2025 &bull; 6 min read</small>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center">
            <button class="btn btn-primary px-4">Load More Articles</button>
        </div>

    </section>

@endsection