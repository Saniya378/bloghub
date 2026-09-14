@extends('frontend.layouts.app')

@section('title', 'BlogHub - The Future of Artificial Intelligence')

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5">

        <div class="row g-5">

            <!-- LEFT: Article content -->
            <div class="col-lg-8">

                <span class="badge bg-danger mb-3">TECHNOLOGY</span>
                <h1 class="fw-bold mb-3">The Future of Artificial Intelligence</h1>

                <div class="d-flex align-items-center text-secondary small mb-4">
                    <a href="{{ url('/author') }}" class="text-decoration-none">By Sarah Johnson</a>
                    <span class="mx-2">&bull;</span>
                    <span>May 16, 2025</span>
                    <span class="mx-2">&bull;</span>
                    <span>5 min read</span>
                </div>

                <img src="{{ asset('assets/technology.png') }}" class="img-fluid rounded-3 mb-4" alt="Article cover">

                <p class="text-secondary mb-4">
                    Artificial Intelligence (AI) is no longer a concept of the future — it is here
                    and now. From healthcare to finance, education to entertainment, AI is
                    revolutionising the way we live and work.
                </p>

                <h4 class="fw-bold mb-3">1. What is Artificial Intelligence?</h4>
                <p class="text-secondary mb-4">
                    AI refers to the simulation of human intelligence in machines that are
                    programmed to think and learn like humans. These systems can analyse data,
                    recognise patterns and make decisions with minimal human involvement.
                </p>

                <h4 class="fw-bold mb-3">2. Applications Across Industries</h4>
                <p class="text-secondary mb-4">
                    AI is being used in healthcare for diagnosis, in finance for fraud detection,
                    and in transportation for self driving vehicles. Its reach continues to expand
                    into nearly every sector.
                </p>

                <h4 class="fw-bold mb-3">3. What Lies Ahead</h4>
                <p class="text-secondary mb-0">
                    As AI continues to evolve, it will bring both opportunities and challenges.
                    Understanding its capabilities and limitations will be key to using it responsibly.
                </p>

            </div>

            <!-- RIGHT: Sidebar -->
            <div class="col-lg-4">

                <div class="border rounded-3 p-4 mb-4">
                    <h6 class="fw-bold mb-3">Categories</h6>
                    <a href="{{ url('/categories') }}" class="text-decoration-none">Technology</a>
                </div>

                <div class="border rounded-3 p-4 mb-4">
                    <h6 class="fw-bold mb-3">Tags</h6>
                    <a href="{{ url('/tag') }}" class="badge bg-light text-dark border me-2 mb-2 text-decoration-none">AI</a>
                    <a href="{{ url('/tag') }}" class="badge bg-light text-dark border me-2 mb-2 text-decoration-none">Machine Learning</a>
                    <a href="{{ url('/tag') }}" class="badge bg-light text-dark border me-2 mb-2 text-decoration-none">Future</a>
                    <a href="{{ url('/tag') }}" class="badge bg-light text-dark border me-2 mb-2 text-decoration-none">Innovation</a>
                    <a href="{{ url('/tag') }}" class="badge bg-light text-dark border mb-2 text-decoration-none">Tech</a>
                </div>

                <div class="border rounded-3 p-4">
                    <h6 class="fw-bold mb-3">Related Posts</h6>

                    <div class="d-flex mb-3 position-relative">
                        <a href="{{ url('/blog') }}" class="stretched-link"></a>
                        <img src="https://loremflickr.com/70/60/5g,network" class="rounded me-3" style="width:70px;height:60px;object-fit:cover;" alt="related">
                        <div>
                            <p class="small fw-semibold mb-0">5G Technology: What It Means for You</p>
                            <small class="text-secondary">May 15, 2025</small>
                        </div>
                    </div>

                    <div class="d-flex mb-3 position-relative">
                        <a href="{{ url('/blog') }}" class="stretched-link"></a>
                        <img src="https://loremflickr.com/70/60/laptop,coding" class="rounded me-3" style="width:70px;height:60px;object-fit:cover;" alt="related">
                        <div>
                            <p class="small fw-semibold mb-0">Best Laptops for Developers in 2025</p>
                            <small class="text-secondary">May 12, 2025</small>
                        </div>
                    </div>

                    <div class="d-flex position-relative">
                        <a href="{{ url('/blog') }}" class="stretched-link"></a>
                        <img src="https://loremflickr.com/70/60/cybersecurity" class="rounded me-3" style="width:70px;height:60px;object-fit:cover;" alt="related">
                        <div>
                            <p class="small fw-semibold mb-0">Cybersecurity Basics Everyone Should Know</p>
                            <small class="text-secondary">May 10, 2025</small>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </section>

    @include('frontend.components.footer')

@endsection