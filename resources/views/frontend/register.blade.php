@extends('frontend.layouts.app')

@section('title', 'BlogHub - Register')

@section('content')

    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <h3 class="fw-bold mb-1">Create Your Account</h3>
                        <p class="text-secondary mb-4">Join BlogHub today!</p>

                        <form>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" placeholder="Enter your full name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" placeholder="Enter your email">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" placeholder="Create a password">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" placeholder="Confirm your password">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agreeTerms">
                                <label class="form-check-label small" for="agreeTerms">
                                    I agree to the <a href="{{ url('/terms') }}" class="text-decoration-none">Terms & Conditions</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Sign Up</button>

                            <p class="text-center small mb-0">
                                Already have an account?
                                <a href="{{ url('/login') }}" class="text-decoration-none">Login</a>
                            </p>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection