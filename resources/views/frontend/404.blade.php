@extends('frontend.layouts.app')

@section('title', 'BlogHub - Page Not Found')

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5 text-center">
        <div class="py-5">
            <i class="bi bi-search fs-1 text-primary mb-3 d-block"></i>
            <h1 class="fw-bold display-3 text-primary">404</h1>
            <h4 class="fw-bold mb-3">Page Not Found</h4>
            <p class="text-secondary mb-4">
                Oops! The page you are looking for doesn't exist or has been moved.
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary px-4">Go to Home Page</a>
        </div>
    </section>

    @include('frontend.components.footer')

@endsection