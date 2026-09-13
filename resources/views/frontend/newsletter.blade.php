@extends('frontend.layouts.app')

@section('title', 'BlogHub - Newsletter')

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="bg-primary bg-opacity-10 border border-primary rounded-4 p-5 text-center mb-5">
                    <i class="bi bi-envelope-paper fs-1 text-primary mb-3 d-block"></i>
                    <h2 class="fw-bold mb-2">Stay Updated</h2>
                    <p class="text-secondary mb-4">
                        Subscribe to our newsletter and never miss the latest articles and updates.
                    </p>

                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Enter your email address">
                                <button class="btn btn-primary px-4">Subscribe</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 text-center">
                    <div class="col-md-4">
                        <i class="bi bi-calendar-check fs-2 text-primary mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1">Daily Updates</h6>
                        <small class="text-secondary">Get the best articles in your inbox daily.</small>
                    </div>
                    <div class="col-md-4">
                        <i class="bi bi-shield-check fs-2 text-primary mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1">No Spam</h6>
                        <small class="text-secondary">We respect your privacy.</small>
                    </div>
                    <div class="col-md-4">
                        <i class="bi bi-x-circle fs-2 text-primary mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1">Unsubscribe Anytime</h6>
                        <small class="text-secondary">You can unsubscribe anytime.</small>
                    </div>
                </div>

            </div>
        </div>
    </section>

    @include('frontend.components.footer')

@endsection