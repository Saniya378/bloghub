@extends('frontend.layouts.app')

@section('title', 'BlogHub - About Us')

@section('content')
@include('frontend.components.navbar')

    <!-- Dark hero banner -->
    <section class="bg-dark text-light py-5">
        <div class="container py-4">
            <h1 class="fw-bold">About BlogHub</h1>
            <p class="text-secondary fs-5 mb-0" style="max-width:600px;">
                We are passionate about sharing knowledge, stories and ideas that inspire.
            </p>
        </div>
    </section>

    <section class="container py-5">

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <h5 class="fw-bold"><i class="bi bi-bullseye text-primary me-2"></i>Our Mission</h5>
                <p class="text-secondary">
                    To create a platform where writers and readers come together to learn, share and grow through meaningful content.
                </p>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold"><i class="bi bi-eye text-primary me-2"></i>Our Vision</h5>
                <p class="text-secondary">
                    To be the most trusted and loved blogging platform for knowledge seekers around the world.
                </p>
            </div>
        </div>

        <!-- Stats -->
        <div class="row text-center g-4 mb-5">
            <div class="col-6 col-md-3">
                <h3 class="fw-bold text-primary mb-0">10K+</h3>
                <small class="text-secondary">Articles Published</small>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="fw-bold text-primary mb-0">5K+</h3>
                <small class="text-secondary">Writers</small>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="fw-bold text-primary mb-0">500K+</h3>
                <small class="text-secondary">Readers</small>
            </div>
            <div class="col-6 col-md-3">
                <h3 class="fw-bold text-primary mb-0">15+</h3>
                <small class="text-secondary">Categories</small>
            </div>
        </div>

        <!-- What We Do -->
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <h4 class="fw-bold mb-3">What We Do</h4>
                <p class="text-secondary">
                    BlogHub is a modern publishing platform covering technology, education, travel, food,
                    business, sports and lifestyle. We connect curious readers with knowledgeable writers
                    from around the world.
                </p>
            </div>
            <div class="col-md-6">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQTQiy5lmFf6FSnszFXTevZHfuE-yx6k1vK2HYlO1KZDw&s" class="img-fluid rounded-3" alt="Team">
            </div>
        </div>

    </section>

    @include('frontend.components.footer')
@endsection