@extends('frontend.layouts.app')

@section('title', 'BlogHub - Trending Now')

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5">

        <h2 class="fw-bold mb-1">Trending Now</h2>
        <p class="text-secondary mb-5">Most popular articles this week</p>

        <div class="row g-5">

            <!-- LEFT: Trending list -->
            <div class="col-lg-8">

                <div class="d-flex align-items-center border-bottom pb-3 mb-3 position-relative">
                    <a href="{{ url('/blog/future-of-ai') }}" class="stretched-link"></a>
                    <span class="badge bg-primary rounded-circle fs-6 me-3" style="width:32px;height:32px;">1</span>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVTC-sGcFxRBrTzO-WIiUVUCpS3egZD3PBnug8tz5LzA&s=10" class="rounded me-3" style="width:100px;height:70px;object-fit:cover;" alt="thumbnail">
                    <div>
                        <h6 class="mb-1">The Future of Artificial Intelligence</h6>
                        <small class="text-secondary">May 16, 2025 &bull; 5 min read</small>
                    </div>
                </div>

                <div class="d-flex align-items-center border-bottom pb-3 mb-3 position-relative">
                    <a href="{{ url('/blog/maldives-guide') }}" class="stretched-link"></a>
                    <span class="badge bg-primary rounded-circle fs-6 me-3" style="width:32px;height:32px;">2</span>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXLHedoXnRsGLmryCFmkRr67p6DKDhIupswrSYmaBIvg&s=10" class="rounded me-3" style="width:100px;height:70px;object-fit:cover;" alt="thumbnail">
                    <div>
                        <h6 class="mb-1">Exploring the Maldives: A Complete Guide</h6>
                        <small class="text-secondary">May 16, 2025 &bull; 6 min read</small>
                    </div>
                </div>

                <div class="d-flex align-items-center border-bottom pb-3 mb-3 position-relative">
                    <a href="{{ url('/blog/healthy-recipes') }}" class="stretched-link"></a>
                    <span class="badge bg-primary rounded-circle fs-6 me-3" style="width:32px;height:32px;">3</span>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsU55EnkZObOdYQXWuOqSaKVJqhsqSYja4qEfgK3x-cg&s=10" class="rounded me-3" style="width:100px;height:70px;object-fit:cover;" alt="thumbnail">
                    <div>
                        <h6 class="mb-1">10 Healthy Recipes for a Better You</h6>
                        <small class="text-secondary">May 14, 2025 &bull; 6 min read</small>
                    </div>
                </div>

                <div class="d-flex align-items-center border-bottom pb-3 mb-3 position-relative">
                    <a href="{{ url('/blog/startup-ideas-2025') }}" class="stretched-link"></a>
                    <span class="badge bg-primary rounded-circle fs-6 me-3" style="width:32px;height:32px;">4</span>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNlHdWYiA-2nXpEfxzzu-K8UGzPhMdtek1acZEYbyw0Q&s=10" class="rounded me-3" style="width:100px;height:70px;object-fit:cover;" alt="thumbnail">
                    <div>
                        <h6 class="mb-1">Startup Ideas to Watch in 2025</h6>
                        <small class="text-secondary">May 13, 2025 &bull; 7 min read</small>
                    </div>
                </div>

                <div class="d-flex align-items-center pb-3 position-relative">
                    <a href="{{ url('/blog/cybersecurity-basics') }}" class="stretched-link"></a>
                    <span class="badge bg-primary rounded-circle fs-6 me-3" style="width:32px;height:32px;">5</span>
                    <img src="https://loremflickr.com/100/70/cybersecurity" class="rounded me-3" style="width:100px;height:70px;object-fit:cover;" alt="thumbnail">
                    <div>
                        <h6 class="mb-1">Cybersecurity Basics Everyone Should Know</h6>
                        <small class="text-secondary">May 12, 2025 &bull; 4 min read</small>
                    </div>
                </div>

            </div>

            <!-- RIGHT: Popular categories + Newsletter -->
            <div class="col-lg-4">

                <div class="border rounded-3 p-4 mb-4">
                    <h6 class="fw-bold mb-3">Popular Categories</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-cpu text-primary me-2"></i>Technology</li>
                        <li class="mb-2"><i class="bi bi-mortarboard text-primary me-2"></i>Education</li>
                        <li class="mb-2"><i class="bi bi-airplane text-primary me-2"></i>Travel</li>
                        <li class="mb-2"><i class="bi bi-cup-hot text-primary me-2"></i>Food</li>
                        <li class="mb-2"><i class="bi bi-briefcase text-primary me-2"></i>Business</li>
                        <li><i class="bi bi-heart-pulse text-primary me-2"></i>Health</li>
                    </ul>
                </div>

                <div class="bg-primary bg-opacity-10 border border-primary rounded-3 p-4">
                    <h6 class="fw-bold mb-2">Subscribe to our Newsletter</h6>
                    <p class="text-secondary small mb-3">Get the latest updates and articles.</p>
                    <input type="email" class="form-control mb-2" placeholder="Enter your email">
                    <button class="btn btn-primary w-100">Subscribe</button>
                </div>

            </div>

        </div>

    </section>

@endsection