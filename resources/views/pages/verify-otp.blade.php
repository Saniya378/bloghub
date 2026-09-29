 @extends('layouts.app')

@section('title', 'Verify OTP — BlogHub')
@section('meta_description', 'Enter the verification code sent to your email.')

@section('content')
<section class="bh-auth-page bh-reveal">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="bh-card bh-auth-card">
                    <div class="bh-card-body p-4 p-md-5">
                        <div class="bh-auth-header">
                            <span class="bh-section-kicker">Two-Factor Verification</span>
                            <h1 class="bh-h2 mb-2">Enter Verification Code</h1>
                            <p class="bh-body" style="color: var(--color-text-muted); font-size: 0.9375rem;">
                                We sent a 6-digit code to <strong id="otpEmailDisplay"></strong>. Enter it below to continue.
                            </p>
                        </div>

                        <form id="otpForm" class="bh-auth-form" novalidate>
                            <!-- OTP Input Boxes -->
                            <div class="bh-otp-inputs" id="otpInputs">
                                <input type="text" class="bh-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 1">
                                <input type="text" class="bh-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 2">
                                <input type="text" class="bh-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 3">
                                <input type="text" class="bh-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 4">
                                <input type="text" class="bh-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 5">
                                <input type="text" class="bh-otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" aria-label="Digit 6">
                            </div>

                            <div class="bh-form-feedback" id="otpFeedback" style="display: none;"></div>

                            <button type="submit" class="btn-bh btn-bh-primary w-100" id="otpSubmitBtn">
                                <span class="bh-spinner bh-spinner-sm me-2 d-none" id="otpSpinner"></span>
                                <span id="otpBtnText">Verify Code</span>
                            </button>
                        </form>

                        <!-- Resend Section -->
                        <div class="bh-otp-resend">
                            <p class="bh-otp-resend-text">Didn't receive the code?</p>
                            <button type="button" class="bh-auth-link" id="otpResendBtn" disabled>
                                Resend in <span id="otpCountdown">30</span>s
                            </button>
                        </div>

                        <div class="bh-auth-footer-text mt-4">
                            <a href="/login" class="bh-auth-link"><i class="fa-solid fa-arrow-left me-1"></i> Back to Sign In</a>
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
    const form = document.getElementById('otpForm');
    const boxes = document.querySelectorAll('.bh-otp-box');
    const feedback = document.getElementById('otpFeedback');
    const submitBtn = document.getElementById('otpSubmitBtn');
    const spinner = document.getElementById('otpSpinner');
    const btnText = document.getElementById('otpBtnText');
    const resendBtn = document.getElementById('otpResendBtn');
    const countdownEl = document.getElementById('otpCountdown');

    // Get email from URL or display default
    const params = new URLSearchParams(window.location.search);
    const email = params.get('email') || 'your email';
    document.getElementById('otpEmailDisplay').textContent = email;

    // Focus first box
    boxes[0].focus();

    // OTP input handling
    boxes.forEach((box, index) => {
        // Input event
        box.addEventListener('input', (e) => {
            const value = e.target.value;

            // Only allow digits
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            // Auto-advance to next box
            if (value && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }
        });

        // Keydown event
        box.addEventListener('keydown', (e) => {
            // Backspace - go back to previous box
            if (e.key === 'Backspace' && !box.value && index > 0) {
                boxes[index - 1].focus();
                boxes[index - 1].value = '';
            }

            // Arrow keys navigation
            if (e.key === 'ArrowLeft' && index > 0) {
                boxes[index - 1].focus();
            }
            if (e.key === 'ArrowRight' && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }
        });

        // Paste support
        box.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pasted.replace(/\D/g, '').slice(0, 6);

            if (digits) {
                digits.split('').forEach((digit, i) => {
                    if (boxes[i]) {
                        boxes[i].value = digit;
                    }
                });
                // Focus the next empty box or the last box
                const nextEmpty = Array.from(boxes).find(b => !b.value);
                if (nextEmpty) {
                    nextEmpty.focus();
                } else {
                    boxes[boxes.length - 1].focus();
                }
            }
        });
    });

    // Form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const code = Array.from(boxes).map(b => b.value).join('');

        feedback.style.display = 'none';

        if (code.length !== 6) {
            feedback.textContent = 'Please enter the complete 6-digit code.';
            feedback.style.display = 'block';
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Verifying...';

        // Simulate async verification
        setTimeout(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Verify Code';

            showToast('Verification Successful', 'Your identity has been verified. Redirecting...', 'fa-solid fa-circle-check text-success');

            // Redirect to reset password after delay
            setTimeout(() => {
                window.location.href = '/reset-password';
            }, 1500);
        }, 1200);
    });

    // Resend countdown
    let countdown = 30;
    const countdownInterval = setInterval(() => {
        countdown--;
        countdownEl.textContent = countdown;

        if (countdown <= 0) {
            clearInterval(countdownInterval);
            resendBtn.disabled = false;
            resendBtn.innerHTML = 'Resend Code';
        }
    }, 1000);

    // Resend button click
    resendBtn.addEventListener('click', () => {
        if (resendBtn.disabled) return;

        showToast('Code Resent', 'A new verification code has been sent to your email.', 'fa-solid fa-rotate-left text-info');

        // Reset countdown
        countdown = 30;
        resendBtn.disabled = true;
        resendBtn.innerHTML = 'Resend in <span id="otpCountdown">30</span>s';
        countdownEl = document.getElementById('otpCountdown');

        // Clear boxes
        boxes.forEach(b => b.value = '');
        boxes[0].focus();
    });

    if (window.initScrollReveal) window.initScrollReveal();
});
</script>
@endpush
