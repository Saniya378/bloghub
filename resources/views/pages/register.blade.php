 @extends('layouts.app')

@section('title', 'Create Account — BlogHub')
@section('meta_description', 'Create your BlogHub account to save articles, follow authors, and join the community.')

@section('content')
<section class="bh-auth-page bh-reveal">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="bh-card bh-auth-card">
                    <div class="bh-card-body p-4 p-md-5">
                        <div class="bh-auth-header">
                            <span class="bh-section-kicker">Join BlogHub</span>
                            <h1 class="bh-h2 mb-2">Create Account</h1>
                            <p class="bh-body" style="color: var(--color-text-muted); font-size: 0.9375rem;">
                                Start reading, saving, and engaging with thoughtful content.
                            </p>
                        </div>

                        <form id="registerForm" class="bh-auth-form" method="POST" action="{{ route('register') }}" novalidate>
                            @csrf
                            <div class="bh-form-group">
                                <label for="regName" class="bh-form-label">Full Name</label>
                                <input type="text" id="regName" name="name" class="bh-form-control" placeholder="Your full name" required autocomplete="name" value="{{ old('name') }}">
                            </div>

                            <div class="bh-form-group">
                                <label for="regEmail" class="bh-form-label">Email Address</label>
                                <input type="email" id="regEmail" name="email" class="bh-form-control" placeholder="you@example.com" required autocomplete="email" value="{{ old('email') }}">
                            </div>

                            <div class="bh-form-group">
                                <label for="regPassword" class="bh-form-label">Password</label>
                                <div class="bh-password-wrap">
                                    <input type="password" id="regPassword" name="password" class="bh-form-control" placeholder="Create a strong password" required autocomplete="new-password">
                                    <button type="button" class="bh-password-toggle" id="regPasswordToggle" aria-label="Toggle password visibility">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <!-- Password Strength Indicator -->
                                <div class="bh-password-strength" id="passwordStrength">
                                    <div class="bh-password-strength-bar">
                                        <div class="bh-password-strength-fill" id="strengthFill"></div>
                                    </div>
                                    <span class="bh-password-strength-text" id="strengthText">Enter a password</span>
                                </div>
                            </div>

                            <div class="bh-form-group">
                                <label for="regConfirmPassword" class="bh-form-label">Confirm Password</label>
                                <div class="bh-password-wrap">
                                    <input type="password" id="regConfirmPassword" name="password_confirmation" class="bh-form-control" placeholder="Confirm your password" required autocomplete="new-password">
                                    <button type="button" class="bh-password-toggle" id="regConfirmToggle" aria-label="Toggle password visibility">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="bh-form-check mb-4">
                                <input type="checkbox" id="termsCheck" name="terms" class="bh-form-check-input" value="1">
                                <label for="termsCheck" class="bh-form-check-label">
                                    I agree to the <a href="/terms" class="bh-auth-link">Terms of Service</a> and <a href="/privacy" class="bh-auth-link">Privacy Policy</a>
                                </label>
                            </div>

                            @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <div class="bh-form-feedback" id="registerFeedback" style="display: none;"></div>

                            <button type="submit" class="btn-bh btn-bh-primary w-100" id="registerSubmitBtn">
                                <span class="bh-spinner bh-spinner-sm me-2 d-none" id="registerSpinner"></span>
                                <span id="registerBtnText">Create Account</span>
                            </button>
                        </form>

                        <div class="bh-auth-divider">
                            <span>or</span>
                        </div>

                        <div class="bh-auth-social">
                            <button type="button" class="btn-bh btn-bh-ghost w-100" style="height: 44px;">
                                <i class="fa-brands fa-google me-2"></i> Sign up with Google
                            </button>
                            <button type="button" class="btn-bh btn-bh-ghost w-100" style="height: 44px;">
                                <i class="fa-brands fa-github me-2"></i> Sign up with GitHub
                            </button>
                        </div>

                        <p class="bh-auth-footer-text">
                            Already have an account? <a href="/login" class="bh-auth-link">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');
    const nameInput = document.getElementById('regName');
    const emailInput = document.getElementById('regEmail');
    const passwordInput = document.getElementById('regPassword');
    const confirmInput = document.getElementById('regConfirmPassword');
    const termsCheck = document.getElementById('termsCheck');
    const feedback = document.getElementById('registerFeedback');
    const submitBtn = document.getElementById('registerSubmitBtn');
    const spinner = document.getElementById('registerSpinner');
    const btnText = document.getElementById('registerBtnText');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');

    // Password visibility toggles
    document.getElementById('regPasswordToggle').addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        document.getElementById('regPasswordToggle').querySelector('i').className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });

    document.getElementById('regConfirmToggle').addEventListener('click', () => {
        const isPassword = confirmInput.type === 'password';
        confirmInput.type = isPassword ? 'text' : 'password';
        document.getElementById('regConfirmToggle').querySelector('i').className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });

    // Password strength indicator
    passwordInput.addEventListener('input', () => {
        const password = passwordInput.value;
        let strength = 0;
        let label = '';
        let color = '';

        if (password.length === 0) {
            label = 'Enter a password';
            color = 'var(--color-border)';
        } else {
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            if (strength <= 2) {
                label = 'Weak';
                color = 'var(--color-danger)';
            } else if (strength <= 3) {
                label = 'Fair';
                color = 'var(--color-warning)';
            } else if (strength <= 4) {
                label = 'Good';
                color = 'var(--color-info)';
            } else {
                label = 'Strong';
                color = 'var(--color-success)';
            }
        }

        const percentage = password.length === 0 ? 0 : Math.min((strength / 5) * 100, 100);
        strengthFill.style.width = percentage + '%';
        strengthFill.style.backgroundColor = color;
        strengthText.textContent = label;
        strengthText.style.color = color;
    });

    // Form submission — client-side validation only; let native form POST to server
    form.addEventListener('submit', (e) => {
        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Reset validation
        [nameInput, emailInput, passwordInput, confirmInput].forEach(el => el.classList.remove('is-invalid'));
        feedback.style.display = 'none';

        // Validate
        let hasError = false;
        if (!name) {
            nameInput.classList.add('is-invalid');
            hasError = true;
        }
        if (!email || !emailRegex.test(email)) {
            emailInput.classList.add('is-invalid');
            hasError = true;
        }
        if (!password || password.length < 8) {
            passwordInput.classList.add('is-invalid');
            hasError = true;
        }
        if (password !== confirm) {
            confirmInput.classList.add('is-invalid');
            hasError = true;
        }
        if (!termsCheck.checked) {
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            feedback.textContent = 'Please fill in all fields correctly and accept the terms.';
            feedback.style.display = 'block';
            return;
        }

        // Show loading state while form posts to server
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Creating account...';
        // Allow natural form submission to proceed
    });

    if (window.initScrollReveal) window.initScrollReveal();
});
</script>
@endpush
