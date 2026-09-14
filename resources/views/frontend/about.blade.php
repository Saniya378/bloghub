@extends('frontend.layouts.app')

@section('title', 'BlogHub - Sarah Johnson')

@section('content')
@include('frontend.components.navbar')

    <!-- Cover banner -->
    <section class="bg-dark" style="height:180px;"></section>

    <section class="container">

        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-end mt-n5 mb-4">
                    <img src="https://loremflickr.com/120/120/woman,portrait" class="rounded-circle border border-4 border-white" alt="Sarah Johnson">
                    <div class="ms-3 pb-2">
                        <h4 class="fw-bold mb-0">Sarah Johnson</h4>
                        <p class="text-secondary mb-0">Writer &bull; Technology Enthusiast</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-secondary mb-4" style="max-width:600px;">
            Passionate about technology and the impact of AI in our daily lives.
        </p>

        <!-- Stats -->
        <div class="d-flex gap-5 mb-4">
            <div>
                <h5 class="fw-bold mb-0">25</h5>
                <small class="text-secondary">Articles</small>
            </div>
            <div>
                <h5 class="fw-bold mb-0">12K</h5>
                <small class="text-secondary">Followers</small>
            </div>
            <div>
                <h5 class="fw-bold mb-0">8K</h5>
                <small class="text-secondary">Following</small>
            </div>
        </div>

        <div class="mb-5">
            <i class="bi bi-facebook fs-5 text-primary me-3"></i>
            <i class="bi bi-twitter fs-5 text-primary me-3"></i>
            <i class="bi bi-linkedin fs-5 text-primary"></i>
        </div>

        <!-- Latest articles -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Latest Articles</h5>
            <a href="{{ url('/trending') }}" class="text-primary text-decoration-none small">View All &rarr;</a>
        </div>

        <div class="row g-4 pb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVTC-sGcFxRBrTzO-WIiUVUCpS3egZD3PBnug8tz5LzA&s=10" class="card-img-top" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">The Future of Artificial Intelligence</h6>
                        <small class="text-secondary">May 16, 2025 &bull; 5 min read</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://loremflickr.com/300/180/5g,network" class="card-img-top" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">5G Technology: What It Means for You</h6>
                        <small class="text-secondary">May 15, 2025 &bull; 5 min read</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://loremflickr.com/300/180/laptop,coding" class="card-img-top" alt="article">
                    <div class="card-body">
                        <h6 class="card-title">Best Laptops for Developers in 2025</h6>
                        <small class="text-secondary">May 12, 2025 &bull; 6 min read</small>
                    </div>
                </div>
            </div>
        </div>

    </section>

    @include('frontend.components.footer')

@endsection