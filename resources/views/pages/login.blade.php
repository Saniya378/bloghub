@extends('layouts.app')

@section('title', 'Sign In — BlogHub')

@section('meta_description', 'Sign in to your BlogHub account to access saved articles, follow authors, and join the conversation.')
@section('content')

<section class="bh-auth-page bh-reveal">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="bh-card bh-auth-card">
                    <div class="bh-card-body p-4 p-md-5">
                        <div class="bh-auth-header">
                            <span class="bh-section-kicker">Welcome Back</span>
                            <h1 class="bh-h2 mb-2">Sign In</h1>
                            <p class="bh-body" style="color: var(--color-text-muted); font-size: 0.9375rem;">
                                Enter your credentials to access your account.
                            </p>
                        </div>

                        <form id="loginForm" class="bh-auth-form" method="POST" action="{{ route('login') }}" novalidate>
                            @csrf
                            <div class="bh-form-group">
                                <label for="loginEmail" class="bh-form-label">Email or Username</label>
                                <input type="text" id="loginEmail" name="email" class="bh-form-control" placeholder="you@example.com or username" required autocomplete="username" value="{{ old('email') }}">
                            </div>

                            <div class="bh-form-group">
                                <label for="loginPassword" class="bh-form-label">Password</label>
                                <div class="bh-password-wrap">
                                    <input type="password" id="loginPassword" name="password" class="bh-form-control" placeholder="Enter your password" required autocomplete="current-password">
                                    <button type="button" class="bh-password-toggle" id="loginPasswordToggle" aria-label="Toggle password visibility">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="bh-form-check d-flex align-items-center gap-2">
                                    <input type="checkbox" id="rememberMe" name="remember" class="bh-form-check-input">
                                    <label for="rememberMe" class="bh-form-check-label mb-0">Remember me</label>
                                </div>
                                <a href="{{ url('/forgot-password') }}" class="bh-auth-link">Forgot password?</a>
                            </div>

                            @if($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                            @endif

                            @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                            @endif

                            <div class="bh-form-feedback" id="loginFeedback" style="display: none;"></div>

                            <button type="submit" class="btn-bh btn-bh-primary w-100" id="loginSubmitBtn">
                                <span class="bh-spinner bh-spinner-sm me-2 d-none" id="loginSpinner"></span>
                                <span id="loginBtnText">Sign In</span>
                            </button>
                        </form>

                        <div class="bh-auth-divider">
                            <span>or</span>
                        </div>

                        <div class="bh-auth-social">
                            <button type="button" class="btn-bh btn-bh-ghost w-100" style="height: 44px;">
                                <i class="fa-brands fa-google me-2"></i> Continue with Google
                            </button>
                            <button type="button" class="btn-bh btn-bh-ghost w-100" style="height: 44px;">
                                <i class="fa-brands fa-github me-2"></i> Continue with GitHub
                            </button>
                        </div>

                        <p class="bh-auth-footer-text">
                            Don't have an account? <a href="/register" class="bh-auth-link">Create one</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection