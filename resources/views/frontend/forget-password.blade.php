@extends('frontend.layouts.app')

@section('title', 'BlogHub - Forgot Password')

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <h3 class="fw-bold mb-1">Forgot Password?</h3>
                        <p class="text-secondary mb-4">
                            No worries! Enter your email address below and we'll send you a link to reset your password.
                        </p>

                        <form>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" placeholder="you@example.com">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Link</button>

                            <p class="text-center small mb-0">
                                Remember your password?
                                <a href="{{ url('/login') }}" class="text-decoration-none">Back to Login</a>
                            </p>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>

    @include('frontend.components.footer')

@endsection