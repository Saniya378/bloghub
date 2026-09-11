@extends('frontend.layouts.app')

@section('title', 'BlogHub - Search Results')

@section('content')

    <section class="container py-5">

        <h2 class="fw-bold mb-1">Search Results for "technology"</h2>
        <p class="text-secondary mb-4">12 results found</p>

        <div class="input-group mb-5" style="max-width:500px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" value="technology" placeholder="Search articles...">
        </div>

        <div class="row g-5">

            <!-- LEFT: Results -->
            <div class="col-lg-8">

                <div class="d-flex mb-4 pb-4 border-bottom position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/120x90" class="rounded me-3" alt="thumbnail">
                    <div>
                        <h6 class="fw-bold mb-1">The Future of Artificial Intelligence</h6>
                        <small class="text-secondary">May 16, 2025 &bull; 5 min read</small>
                    </div>
                </div>

                <div class="d-flex mb-4 pb-4 border-bottom position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/120x90" class="rounded me-3" alt="thumbnail">
                    <div>
                        <h6 class="fw-bold mb-1">5G Technology: What It Means for You</h6>
                        <small class="text-secondary">May 15, 2025 &bull; 5 min read</small>
                    </div>
                </div>

                <div class="d-flex mb-4 pb-4 border-bottom position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/120x90" class="rounded me-3" alt="thumbnail">
                    <div>
                        <h6 class="fw-bold mb-1">Best Laptops for Developers in 2025</h6>
                        <small class="text-secondary">May 12, 2025 &bull; 6 min read</small>
                    </div>
                </div>

                <div class="d-flex mb-4 pb-4 border-bottom position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/120x90" class="rounded me-3" alt="thumbnail">
                    <div>
                        <h6 class="fw-bold mb-1">Top 10 Tech Gadgets in 2025</h6>
                        <small class="text-secondary">May 9, 2025 &bull; 5 min read</small>
                    </div>
                </div>

                <div class="d-flex position-relative">
                    <a href="{{ url('/blog') }}" class="stretched-link"></a>
                    <img src="https://placehold.co/120x90" class="rounded me-3" alt="thumbnail">
                    <div>
                        <h6 class="fw-bold mb-1">How Technology is Changing Education</h6>
                        <small class="text-secondary">May 7, 2025 &bull; 4 min read</small>
                    </div>
                </div>

            </div>

            <!-- RIGHT: Filter -->
            <div class="col-lg-4">
                <div class="border rounded-3 p-4">
                    <h6 class="fw-bold mb-3">Filter by Category</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between mb-2">
                            <span>Technology</span><span class="text-secondary">(12)</span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Education</span><span class="text-secondary">(8)</span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Business</span><span class="text-secondary">(5)</span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Travel</span><span class="text-secondary">(3)</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Health</span><span class="text-secondary">(1)</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </section>

@endsection