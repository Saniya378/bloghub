 @extends('layouts.app')

@section('title', 'Reset Password — BlogHub')
@section('meta_description', 'Set a new password for your BlogHub account.')

@section('content')
<section class="bh-auth-page bh-reveal">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="bh-card bh-auth-card">
                    <div class="bh-card-body p-4 p-md-5">
                        <div class="bh-auth-header">
                            <span class="bh-section-kicker">Set New Password</span>
                            <h1 class="bh-h2 mb-2">Reset Password</h1>
                            <p class="bh-body" style="color: var(--color-text-muted); font-size: 0.9375rem;">
                                Create a new password for your account. Make sure it is strong and unique.
                            </p>
                        </div>

                        <!-- Reset Form -->
                        <form id="resetForm" class="bh-auth-form" novalidate>
                            <div class="bh-form-group">
                                <label for="resetPassword" class="bh-form-label">New Password</label>
                                <div class="bh-password-wrap">
                                    <input type="password" id="resetPassword" class="bh-form-control" placeholder="Enter new password" required autocomplete="new-password">
                                    <button type="button" class="bh-password-toggle" id="resetPasswordToggle" aria-label="Toggle password visibility">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <!-- Password Strength Indicator -->
                                <div class="bh-password-strength" id="resetStrength">
                                    <div class="bh-password-strength-bar">
                                        <div class="bh-password-strength-fill" id="resetStrengthFill"></div>
                                    </div>
                                    <span class="bh-password-strength-text" id="resetStrengthText">Enter a password</span>
                                </div>
                            </div>

                            <div class="bh-form-group">
                                <label for="resetConfirm" class="bh-form-label">Confirm New Password</label>
                                <div class="bh-password-wrap">
                                    <input type="password" id="resetConfirm" class="bh-form-control" placeholder="Confirm new password" required autocomplete="new-password">
                                    <button type="button" class="bh-password-toggle" id="resetConfirmToggle" aria-label="Toggle password visibility">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="bh-form-feedback" id="resetFeedback" style="display: none;"></div>

                            <button type="submit" class="btn-bh btn-bh-primary w-100" id="resetSubmitBtn">
                                <span class="bh-spinner bh-spinner-sm me-2 d-none" id="resetSpinner"></span>
                                <span id="resetBtnText">Reset Password</span>
                            </button>
                        </form>

                        <!-- Success State -->
                        <div class="bh-auth-confirmation d-none" id="resetSuccess">
                            <div class="bh-auth-confirmation-icon">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <h3 class="bh-auth-confirmation-title">Password Reset Successful</h3>
                            <p class="bh-auth-confirmation-text">
                                Your password has been updated. You can now sign in with your new password.
                            </p>
                            <a href="/login" class="btn-bh btn-bh-primary w-100" style="height: 44px;">
                                Go to Sign In
                            </a>
                        </div>

                        <div class="bh-auth-footer-text mt-4">
                            Remember your password? <a href="/login" class="bh-auth-link">Sign in</a>
                        </div>
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
    const form = document.getElementById('resetForm');
    const passwordInput = document.getElementById('resetPassword');
    const confirmInput = document.getElementById('resetConfirm');
    const feedback = document.getElementById('resetFeedback');
    const submitBtn = document.getElementById('resetSubmitBtn');
    const spinner = document.getElementById('resetSpinner');
    const btnText = document.getElementById('resetBtnText');
    const successState = document.getElementById('resetSuccess');
    const strengthFill = document.getElementById('resetStrengthFill');
    const strengthText = document.getElementById('resetStrengthText');

    // Password visibility toggles
    document.getElementById('resetPasswordToggle').addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        document.getElementById('resetPasswordToggle').querySelector('i').className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });

    document.getElementById('resetConfirmToggle').addEventListener('click', () => {
        const isPassword = confirmInput.type === 'password';
        confirmInput.type = isPassword ? 'text' : 'password';
        document.getElementById('resetConfirmToggle').querySelector('i').className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
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

    // Form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const password = passwordInput.value;
        const confirm = confirmInput.value;

        // Reset validation
        passwordInput.classList.remove('is-invalid');
        confirmInput.classList.remove('is-invalid');
        feedback.style.display = 'none';

        // Validate
        let hasError = false;
        if (!password || password.length < 8) {
            passwordInput.classList.add('is-invalid');
            hasError = true;
        }
        if (password !== confirm) {
            confirmInput.classList.add('is-invalid');
            hasError = true;
        }

        if (hasError) {
            feedback.textContent = 'Passwords must match and be at least 8 characters.';
            feedback.style.display = 'block';
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Resetting...';

        // Simulate async reset
        setTimeout(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Reset Password';

            // Show success state
            form.classList.add('d-none');
            successState.classList.remove('d-none');

            showToast('Password Reset', 'Your password has been successfully updated.', 'fa-solid fa-circle-check text-success');
        }, 1200);
    });

    if (window.initScrollReveal) window.initScrollReveal();
});
</script>
@endpush
