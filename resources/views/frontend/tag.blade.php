@extends('frontend.layouts.app')

@section('title', 'BlogHub - Tag: Artificial Intelligence')

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5">

        <h2 class="fw-bold mb-1">Tag: Artificial Intelligence</h2>
        <p class="text-secondary mb-5">8 articles found</p>

        <div class="row g-4 mb-4">

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVTC-sGcFxRBrTzO-WIiUVUCpS3egZD3PBnug8tz5LzA&s=10" class="rounded-start" style="width:160px;height:140px;object-fit:cover;" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">The Future of Artificial Intelligence</h6>
                        <small class="text-secondary">May 16, 2025 &bull; 5 min read</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://loremflickr.com/160/140/healthcare,medical" class="rounded-start" style="width:160px;height:140px;object-fit:cover;" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">AI in Healthcare: Benefits and Challenges</h6>
                        <small class="text-secondary">May 15, 2025 &bull; 6 min read</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://loremflickr.com/160/140/machinelearning,computer" class="rounded-start" style="width:160px;height:140px;object-fit:cover;" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">Machine Learning Basics for Beginners</h6>
                        <small class="text-secondary">May 11, 2025 &bull; 5 min read</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://loremflickr.com/160/140/artificialintelligence,robot" class="rounded-start" style="width:160px;height:140px;object-fit:cover;" alt="article">
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

    @include('frontend.components.footer')

@endsection