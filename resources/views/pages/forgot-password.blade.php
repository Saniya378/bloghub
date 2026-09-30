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
                        <form id="forgotForm" class="bh-auth-form" method="POST" action="{{ url('/forgot-password') }}" novalidate>
                            @csrf
                            <div class="bh-form-group">
                                <label for="forgotEmail" class="bh-form-label">Email Address</label>
                                <input type="email" id="forgotEmail" name="email" class="bh-form-control" placeholder="you@example.com" required autocomplete="email">
                            </div>

                            <div class="bh-form-feedback" id="forgotFeedback" style="display: none;"></div>

                            <button type="submit" class="btn-bh btn-bh-primary w-100" id="forgotSubmitBtn">
                                <span class="bh-spinner bh-spinner-sm me-2 d-none" id="forgotSpinner"></span>
                                <span id="forgotBtnText">Send Reset Code</span>
                            </button>
                        </form>

                        <!-- Confirmation State -->
                        <div class="bh-auth-confirmation d-none" id="forgotConfirmation">
                            <div class="bh-auth-confirmation-icon">
                                <i class="fa-solid fa-envelope-circle-check"></i>
                            </div>
                            <h3 class="bh-auth-confirmation-title">Check Your Email</h3>
                            <p class="bh-auth-confirmation-text">
                                We have sent a 6-digit verification code to <strong id="forgotEmailDisplay"></strong>.
                            </p>
                            <div id="devOtpBox" class="alert alert-info py-2 px-3 mb-3 d-none">
                                <small><strong>Demo/Dev OTP:</strong> <span id="devOtpCode" class="fw-bold fs-6 font-monospace"></span></small>
                            </div>
                            <a href="#" class="btn-bh btn-bh-primary w-100 mb-2" id="goToVerifyBtn" style="height: 44px;">
                                Enter Verification Code
                            </a>
                            <button type="button" class="btn-bh btn-bh-ghost w-100" id="forgotResendBtn">
                                <i class="fa-solid fa-rotate-left me-1"></i> Send Again
                            </button>
                        </div>

                        <div class="bh-auth-footer-text mt-4">
                            Remember your password? <a href="{{ url('/login') }}" class="bh-auth-link">Sign in</a>
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
    const form = document.getElementById('forgotForm');
    const emailInput = document.getElementById('forgotEmail');
    const feedback = document.getElementById('forgotFeedback');
    const submitBtn = document.getElementById('forgotSubmitBtn');
    const spinner = document.getElementById('forgotSpinner');
    const btnText = document.getElementById('forgotBtnText');
    const confirmation = document.getElementById('forgotConfirmation');
    const emailDisplay = document.getElementById('forgotEmailDisplay');
    const resendBtn = document.getElementById('forgotResendBtn');
    const goToVerifyBtn = document.getElementById('goToVerifyBtn');
    const devOtpBox = document.getElementById('devOtpBox');
    const devOtpCode = document.getElementById('devOtpCode');

    async function sendOtp(email) {
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Sending...';
        feedback.style.display = 'none';

        try {
            const res = await fetch('{{ url("/forgot-password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                emailDisplay.textContent = email;
                form.classList.add('d-none');
                confirmation.classList.remove('d-none');
                goToVerifyBtn.href = '{{ url("/verify-otp") }}?email=' + encodeURIComponent(email);

                if (data.otp) {
                    devOtpCode.textContent = data.otp;
                    devOtpBox.classList.remove('d-none');
                }

                if (typeof showToast === 'function') {
                    showToast('Code Sent', data.message || 'Verification code sent.', 'fa-solid fa-circle-check text-success');
                }
            } else {
                const errMsg = data.errors?.email?.[0] || data.message || data.error || 'Unable to find an account with this email.';
                feedback.textContent = errMsg;
                feedback.style.display = 'block';
                feedback.className = 'bh-form-feedback alert alert-danger p-2 small mt-2';
            }
        } catch (err) {
            feedback.textContent = 'Something went wrong. Please check your connection.';
            feedback.style.display = 'block';
            feedback.className = 'bh-form-feedback alert alert-danger p-2 small mt-2';
        } finally {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Send Reset Code';
        }
    }

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
            feedback.className = 'bh-form-feedback alert alert-danger p-2 small mt-2';
            return;
        }

        sendOtp(email);
    });

    resendBtn.addEventListener('click', () => {
        const email = emailInput.value.trim();
        if (email) {
            sendOtp(email);
        } else {
            confirmation.classList.add('d-none');
            form.classList.remove('d-none');
        }
    });

    if (window.initScrollReveal) window.initScrollReveal();
});
</script>
@endpush
