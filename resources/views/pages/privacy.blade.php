@extends('layouts.app')

@section('title', 'Privacy Policy — BlogHub')
@section('meta_description', 'BlogHub privacy policy. Learn how we collect, use, and protect your personal information.')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Sticky TOC -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="h6 fw-bold mb-3">Table of Contents</h2>
                            <nav class="nav flex-column">
                                <a class="nav-link toc-link" href="#section-1">1. Information We Collect</a>
                                <a class="nav-link toc-link" href="#section-2">2. How We Use Your Information</a>
                                <a class="nav-link toc-link" href="#section-3">3. Data Sharing</a>
                                <a class="nav-link toc-link" href="#section-4">4. Data Security</a>
                                <a class="nav-link toc-link" href="#section-5">5. Your Rights</a>
                                <a class="nav-link toc-link" href="#section-6">6. Cookies</a>
                                <a class="nav-link toc-link" href="#section-7">7. Third-Party Services</a>
                                <a class="nav-link toc-link" href="#section-8">8. Changes to This Policy</a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-8">
                <span class="text-primary fw-semibold">Legal</span>
                <h1 class="h2 fw-bold mb-2">Privacy Policy</h1>
                <p class="text-muted mb-5">Last updated: {{ now()->format('F j, Y') }}</p>

                <div class="mb-5">
                    <h2 id="section-1" class="h4 fw-bold">1. Information We Collect</h2>
                    <p class="text-muted">We collect information you provide directly to us, such as your name, email address, and profile information when you register for an account. We also collect content you publish, including articles, comments, and messages.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-2" class="h4 fw-bold">2. How We Use Your Information</h2>
                    <p class="text-muted">We use the information we collect to provide, maintain, and improve our services. This includes displaying your content, processing transactions, sending you notifications, and personalizing your experience. We do not sell your personal information to third parties.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-3" class="h4 fw-bold">3. Data Sharing</h2>
                    <p class="text-muted">We share information only as necessary to provide our services. This includes sharing with hosting providers, email delivery services, and analytics providers who help us understand how our platform is used. All third parties are required to protect your data.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-4" class="h4 fw-bold">4. Data Security</h2>
                    <p class="text-muted">We implement industry-standard security measures to protect your personal information. This includes encryption in transit and at rest, regular security audits, and access controls. However, no method of transmission over the internet is 100% secure.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-5" class="h4 fw-bold">5. Your Rights</h2>
                    <p class="text-muted">You have the right to access, correct, or delete your personal information at any time. You can do this through your profile settings or by contacting us. You also have the right to opt out of marketing communications and to request a copy of your data.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-6" class="h4 fw-bold">6. Cookies</h2>
                    <p class="text-muted">We use cookies and similar technologies to remember your preferences, keep you signed in, and understand how you use our platform. You can control cookies through your browser settings, but disabling cookies may affect functionality.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-7" class="h4 fw-bold">7. Third-Party Services</h2>
                    <p class="text-muted">Our platform may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. We encourage you to review the privacy policies of any third-party services you use.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-8" class="h4 fw-bold">8. Changes to This Policy</h2>
                    <p class="text-muted">We may update this privacy policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the "Last updated" date. Your continued use of the platform after changes constitutes acceptance of the new policy.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.toc-link');
    const sections = document.querySelectorAll('[id^="section-"]');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                links.forEach(link => link.classList.remove('active'));
                const activeLink = document.querySelector(`.toc-link[href="#${entry.target.id}"]`);
                if (activeLink) activeLink.classList.add('active');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    sections.forEach(section => observer.observe(section));
});
</script>
@endpush
