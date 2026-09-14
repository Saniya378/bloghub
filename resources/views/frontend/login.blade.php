@extends('frontend.layouts.app')

@section('title', 'BlogHub - Login')

@section('content')

    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <h3 class="fw-bold mb-1">Welcome Back!</h3>
                        <p class="text-secondary mb-4">Login to continue to BlogHub</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Password</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label small" for="rememberMe">Remember Me</label>
                                </div>
                                <a href="#" class="small text-decoration-none">Forgot Password?</a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>

                            <div class="text-center text-secondary small mb-3">or login with</div>

                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-outline-secondary w-50">
                                    <i class="bi bi-google me-1"></i> Google
                                </button>
                                <button type="button" class="btn btn-outline-secondary w-50">
                                    <i class="bi bi-facebook me-1"></i> Facebook
                                </button>
                            </div>

                            <p class="text-center small mb-0">
                                Don't have an account?
                                <a href="{{ url('/register') }}" class="text-decoration-none">Sign Up</a>
                            </p>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection