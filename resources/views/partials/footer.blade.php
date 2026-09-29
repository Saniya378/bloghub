<footer class="bh-footer" style="background-color: var(--color-surface); border-top: 1px solid var(--color-border); margin-top: auto; padding: var(--space-12) 0 var(--space-6) 0;">
    <div class="container">
        <div class="row g-4 mb-5">
            <!-- Brand Column -->
            <div class="col-lg-4 col-md-6">
                <a class="d-flex align-items-center gap-2 mb-3" href="{{ url('/') }}" style="text-decoration: none;">
                    <span class="d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background-color: var(--color-primary); color: #FFFFFF; border-radius: var(--border-radius); font-family: var(--font-heading); font-weight: 700; font-size: 1.15rem;">
                        B
                    </span>
                    <span style="font-family: var(--font-heading); font-size: 1.4rem; font-weight: 800; color: var(--color-primary); letter-spacing: -0.03em;">
                        Blog<span style="color: var(--color-accent);">Hub</span>
                    </span>
                </a>
                <p class="bh-body mb-4" style="color: var(--color-text-muted); max-width: 320px; font-size: 0.9375rem;">
                    A modern multi-category publishing platform where independent writers and staff authors publish long-form essays, tech deep dives, and creative perspectives.
                </p>
                <div class="d-flex align-items-center gap-2">
                    <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="btn-bh btn-bh-ghost btn-bh-icon" aria-label="Follow BlogHub on X (formerly Twitter)" title="Twitter">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="https://github.com" target="_blank" rel="noopener noreferrer" class="btn-bh btn-bh-ghost btn-bh-icon" aria-label="BlogHub GitHub repository" title="GitHub">
                        <i class="fa-brands fa-github"></i>
                    </a>
                    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="btn-bh btn-bh-ghost btn-bh-icon" aria-label="Connect with BlogHub on LinkedIn" title="LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="btn-bh btn-bh-ghost btn-bh-icon" aria-label="Watch BlogHub on YouTube" title="YouTube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="col-lg-2 col-md-6 col-6">
                <h3 style="font-family: var(--font-heading); font-size: 1.125rem; color: var(--color-primary); margin-bottom: var(--space-4);">Quick Links</h3>
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ url('/') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Home</a></li>
                    <li><a href="{{ url('/blogs') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Explore Blogs</a></li>
                    <li><a href="{{ url('/categories') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Categories</a></li>
                    <li><a href="{{ url('/authors') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Authors Directory</a></li>
                    <li><a href="{{ url('/about') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">About BlogHub</a></li>
                    <li><a href="{{ url('/contact') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Contact Support</a></li>
                    <li><a href="{{ url('/ui-kit') }}" style="color: var(--color-accent); text-decoration: none; font-size: 0.9375rem; font-weight: 500;">UI Kit / Tokens</a></li>
                </ul>
            </div>

            <!-- Category Shortcuts Column -->
            <div class="col-lg-2 col-md-6 col-6">
                <h3 style="font-family: var(--font-heading); font-size: 1.125rem; color: var(--color-primary); margin-bottom: var(--space-4);">Categories</h3>
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ url('/blogs?category=technology') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Technology</a></li>
                    <li><a href="{{ url('/blogs?category=design') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Design &amp; UI</a></li>
                    <li><a href="{{ url('/blogs?category=culture') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Culture &amp; Art</a></li>
                    <li><a href="{{ url('/blogs?category=business') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Business</a></li>
                    <li><a href="{{ url('/blogs?category=science') }}" style="color: var(--color-text-muted); text-decoration: none; font-size: 0.9375rem;">Science</a></li>
                    <li><a href="{{ url('/categories') }}" style="color: var(--color-accent); text-decoration: none; font-size: 0.9375rem; font-weight: 500;">All 10+ Categories &rarr;</a></li>
                </ul>
            </div>

            <!-- Newsletter Subscription Column -->
            <div class="col-lg-4 col-md-6">
                <h3 style="font-family: var(--font-heading); font-size: 1.125rem; color: var(--color-primary); margin-bottom: var(--space-2);">The Weekly Dispatch</h3>
                <p class="bh-body mb-3" style="color: var(--color-text-muted); font-size: 0.9375rem;">
                    Curated long-form articles, thoughtful essays, and technical updates delivered to your inbox every Sunday.
                </p>
                <form id="footerNewsletterForm" novalidate onsubmit="handleFooterNewsletter(event, this)">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex gap-2">
                            <label for="footerNewsletterEmail" class="visually-hidden">Email Address for Newsletter</label>
                            <input type="email" id="footerNewsletterEmail" class="bh-form-control" placeholder="reader@domain.com" required style="border-radius: var(--border-radius);">
                            <button type="submit" class="btn-bh btn-bh-accent" style="flex-shrink: 0;">
                                Subscribe
                            </button>
                        </div>
                        <div id="footerNewsletterFeedback" class="bh-form-feedback" style="display: none;"></div>
                        <small class="bh-caption" style="color: var(--color-text-light);">No spam or ads. Unsubscribe anytime with one click.</small>
                    </div>
                </form>
            </div>
        </div>

        <hr style="border-color: var(--color-border); margin: 0 0 var(--space-6) 0;">

        <!-- Copyright & Legal Links -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <p class="bh-caption mb-0" style="color: var(--color-text-muted);">
                &copy; <span id="footerCurrentYear"></span> BlogHub Pvt. Ltd. All rights reserved.
            </p>
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <a href="{{ url('/privacy') }}" class="bh-caption" style="color: var(--color-text-muted); text-decoration: none;">Privacy Policy</a>
                <a href="{{ url('/terms') }}" class="bh-caption" style="color: var(--color-text-muted); text-decoration: none;">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<script>
    function handleFooterNewsletter(e, form) {
        e.preventDefault();
        const input = form.querySelector('#footerNewsletterEmail');
        const feedback = form.querySelector('#footerNewsletterFeedback');
        const email = input.value.trim();

        if (!email || !email.includes('@') || !email.includes('.')) {
            input.classList.add('is-invalid');
            feedback.textContent = 'Please enter a valid email address.';
            feedback.style.display = 'block';
            return;
        }

        input.classList.remove('is-invalid');
        feedback.style.display = 'none';
        input.value = '';
        if (typeof showToast === 'function') {
            showToast('Subscribed Successfully', 'Welcome to The Weekly Dispatch!', 'fa-solid fa-circle-check text-success');
        }
    }
</script>
