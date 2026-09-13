@extends('frontend.layouts.app')

@section('title', 'BlogHub - All Categories')

@section('content')
@include('frontend.components.navbar')
    <section class="container py-5">

        <h2 class="fw-bold mb-2">All Categories</h2>
        <p class="text-secondary mb-5">Explore articles by your favorite topics</p>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/technology') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsIB1YL5sswq8FeZEgQ2G4OMTL_XOyYjTD8Lrx1Nuefg&s=10" class="card-img-top" alt="Technology">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Technology</h5>
                        <p class="card-text text-secondary small mb-0">120 Articles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/education') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQxiyB2U-IEam9OEIEl84scy5J_lIlEOEdKSUuOvFgaEg&s=10" class="card-img-top" alt="Education">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Education</h5>
                        <p class="card-text text-secondary small mb-0">95 Articles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/travel') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsB_uULOq4wWr5HdAK3CuFNCB8mVWFxvseEBZLd0KP_g&s=10" class="card-img-top" alt="Travel">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Travel</h5>
                        <p class="card-text text-secondary small mb-0">80 Articles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/food') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ9CHAjzKqWLdLyTsKTsHXygnoq9ux6eJ3Rqq136Gae0A&s=10" class="card-img-top" alt="Food">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Food</h5>
                        <p class="card-text text-secondary small mb-0">60 Articles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/business') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4WXxePucQD79UNiVZ3n6RQ-WlLifCfVUu9TVCih_uaw&s=10" class="card-img-top" alt="Business">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Business</h5>
                        <p class="card-text text-secondary small mb-0">70 Articles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/health') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR8g8KX6kc2MfSN4dGfSNTVVj_nfnVMPncFMH5tgzHBAw&s=10" class="card-img-top" alt="Health">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Health</h5>
                        <p class="card-text text-secondary small mb-0">40 Articles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/sports') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRlb6RIDyPC0KWtsWC31gupRsFijGW0bAKucvNAnq4MJA&s=10" class="card-img-top" alt="Sports">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Sports</h5>
                        <p class="card-text text-secondary small mb-0">50 Articles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/lifestyle') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQq__KGLWwHS9dyTaFq_N-lHS06hWxUWbSBJ-Z4ovLMXA&s=10" class="card-img-top" alt="Lifestyle">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Lifestyle</h5>
                        <p class="card-text text-secondary small mb-0">45 Articles</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 position-relative">
                    <a href="{{ url('/category/entertainment') }}" class="stretched-link"></a>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQq__KGLWwHS9dyTaFq_N-lHS06hWxUWbSBJ-Z4ovLMXA&s=10" class="card-img-top" alt="Entertainment">
                    <div class="card-body">
                        <h5 class="card-title mb-1">Entertainment</h5>
                        <p class="card-text text-secondary small mb-0">45 Articles</p>
                    </div>
                </div>
            </div>

        </div>

    </section>

    @include('frontend.components.footer')

@endsection