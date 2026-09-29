 @extends('layouts.app')

@section('title', 'Forgot Password — BlogHub')
@section('meta_description', 'Reset your BlogHub account password.')

@section('content')
<section class="bh-auth-page bh-reveal">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="bh-card bh-auth-card">
                    <div class="bh-card-body p-4 p-md-5">
                        <div class="bh-auth-header">
                            <span class="bh-section-kicker">Reset Password</span>
                            <h1 class="bh-h2 mb-2">Forgot Password?</h1>
                            <p class="bh-body" style="color: var(--color-text-muted); font-size: 0.9375rem;">
                                Enter your email address and we will send you a link to reset your password.
                            </p>
                        </div>

                        <!-- Request Reset Form -->
                        <form id="forgotForm" class="bh-auth-form" novalidate>
                            <div class="bh-form-group">
                                <label for="forgotEmail" class="bh-form-label">Email Address</label>
                                <input type="email" id="forgotEmail" class="bh-form-control" placeholder="you@example.com" required autocomplete="email">
                            </div>

                            <div class="bh-form-feedback" id="forgotFeedback" style="display: none;"></div>

                            <button type="submit" class="btn-bh btn-bh-primary w-100" id="forgotSubmitBtn">
                                <span class="bh-spinner bh-spinner-sm me-2 d-none" id="forgotSpinner"></span>
                                <span id="forgotBtnText">Send Reset Link</span>
                            </button>
                        </form>

                        <!-- Confirmation State -->
                        <div class="bh-auth-confirmation d-none" id="forgotConfirmation">
                            <div class="bh-auth-confirmation-icon">
                                <i class="fa-solid fa-envelope-circle-check"></i>
                            </div>
                            <h3 class="bh-auth-confirmation-title">Check Your Email</h3>
                            <p class="bh-auth-confirmation-text">
                                If an account exists with <strong id="forgotEmailDisplay"></strong>, we have sent a password reset link. Please check your inbox and spam folder.
                            </p>
                            <button type="button" class="btn-bh btn-bh-ghost" id="forgotResendBtn">
                                <i class="fa-solid fa-rotate-left me-1"></i> Resend Link
                            </button>
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('forgotForm');
    const emailInput = document.getElementById('forgotEmail');
    const feedback = document.getElementById('forgotFeedback');
    const submitBtn = document.getElementById('forgotSubmitBtn');
    const spinner = document.getElementById('forgotSpinner');
    const btnText = document.getElementById('forgotBtnText');
    const confirmation = document.getElementById('forgotConfirmation');
    const emailDisplay = document.getElementById('forgotEmailDisplay');
    const resendBtn = document.getElementById('forgotResendBtn');

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        emailInput.classList.remove('is-invalid');
        feedback.style.display = 'none';

        if (!email || !emailRegex.test(email)) {
            emailInput.classList.add('is-invalid');
            feedback.textContent = 'Please enter a valid email address.';
            feedback.style.display = 'block';
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Sending...';

        // Simulate async request
        setTimeout(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Send Reset Link';

            // Show confirmation
            emailDisplay.textContent = email;
            form.classList.add('d-none');
            confirmation.classList.remove('d-none');

            showToast('Reset Link Sent', 'Check your email for password reset instructions.', 'fa-solid fa-circle-check text-success');
        }, 1200);
    });

    // Resend link
    resendBtn.addEventListener('click', () => {
        confirmation.classList.add('d-none');
        form.classList.remove('d-none');
        emailInput.value = '';
        showToast('Link Resent', 'A new password reset link has been sent to your email.', 'fa-solid fa-rotate-left text-info');
    });

    if (window.initScrollReveal) window.initScrollReveal();
});
</script>
@endpush
