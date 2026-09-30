@extends('layouts.app')

@section('title', 'Contact — BlogHub')
@section('meta_description', 'Get in touch with the BlogHub team. We would love to hear from you.')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <span class="text-primary fw-semibold">Get in Touch</span>
                <h1 class="h2 fw-bold mb-4">Contact Us</h1>
                <p class="text-muted mb-4">Have a question, feedback, or partnership inquiry? We would love to hear from you.</p>

                <div class="d-flex flex-column gap-4">
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-envelope fa-lg text-primary mt-1"></i>
                        <div>
                            <div class="fw-semibold">Email</div>
                            <a href="mailto:hello@bloghub.com" class="text-muted">hello@bloghub.com</a>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-location-dot fa-lg text-primary mt-1"></i>
                        <div>
                            <div class="fw-semibold">Office</div>
                            <span class="text-muted">123 Editorial Lane, Berlin, Germany</span>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-clock fa-lg text-primary mt-1"></i>
                        <div>
                            <div class="fw-semibold">Hours</div>
                            <span class="text-muted">Mon–Fri, 9:00–18:00 CET</span>
                        </div>
                    </div>
                </div>

                <!-- Map Placeholder -->
                <div class="mt-4 rounded-4 border d-flex align-items-center justify-content-center" style="height: 200px; background: var(--color-surface-subtle);">
                    <div class="text-center text-muted">
                        <i class="fa-solid fa-map fa-2x mb-2"></i>
                        <div>Map Placeholder</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h4 fw-bold mb-4">Send a Message</h2>
                        <form id="contactForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" required maxlength="255">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required maxlength="255">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" required maxlength="255">
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="5" required maxlength="2000"></textarea>
                                <small class="text-muted"><span id="charCount">0</span> / 2000</small>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <span class="spinner-border spinner-border-sm d-none" id="contactSpinner"></span>
                                    <span id="contactBtnText">Send Message</span>
                                </button>
                            </div>
                            <div id="contactFeedback" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <span class="text-primary fw-semibold">FAQ</span>
                    <h2 class="h3 fw-bold">Frequently Asked Questions</h2>
                </div>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">How do I become an author?</button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">Register as a reader, then visit your profile settings and click "Become an Author". You can immediately start publishing articles.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">How does comment moderation work?</button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">Comments from readers are held for moderation before appearing publicly. Author and admin comments are auto-approved.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">Can I republish my article elsewhere?</button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">Yes. You retain full rights to your work. We ask for a 30-day exclusive window, after which you are free to republish with attribution.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">How is BlogHub funded?</button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">We are bootstrapped and reader-supported. We do not run ads, sell user data, or accept venture capital. Revenue comes from optional subscriptions and author patronage.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">What is your editorial policy?</button>
                        </h3>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">We publish well-researched, original essays that respect the reader's intelligence. We do not publish clickbait, misinformation, or AI-generated content without disclosure.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">How do I report an issue?</button>
                        </h3>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">Use the contact form above with the subject line "Report". Our team reviews all reports within 48 hours.</div>
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
    const form = document.getElementById('contactForm');
    const spinner = document.getElementById('contactSpinner');
    const btnText = document.getElementById('contactBtnText');
    const feedback = document.getElementById('contactFeedback');
    const messageInput = form.querySelector('[name="message"]');
    const charCount = document.getElementById('charCount');

    messageInput.addEventListener('input', () => {
        charCount.textContent = messageInput.value.length;
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        spinner.classList.remove('d-none');
        btnText.textContent = 'Sending...';

        try {
            const formData = new FormData(form);
            const response = await fetch('{{ url("/contact") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                feedback.innerHTML = '<div class="alert alert-success">Thank you! Your message has been sent.</div>';
                form.reset();
                charCount.textContent = '0';
            } else {
                feedback.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Something went wrong.') + '</div>';
            }
        } catch (err) {
            feedback.innerHTML = '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
        } finally {
            spinner.classList.add('d-none');
            btnText.textContent = 'Send Message';
        }
    });
});
</script>
@endpush
