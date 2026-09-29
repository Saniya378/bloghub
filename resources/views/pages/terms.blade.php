@extends('layouts.app')

@section('title', 'Terms of Service — BlogHub')
@section('meta_description', 'BlogHub terms of service. Read the terms that govern your use of our platform.')

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
                                <a class="nav-link toc-link" href="#section-1">1. Acceptance of Terms</a>
                                <a class="nav-link toc-link" href="#section-2">2. User Accounts</a>
                                <a class="nav-link toc-link" href="#section-3">3. Content Ownership</a>
                                <a class="nav-link toc-link" href="#section-4">4. Acceptable Use</a>
                                <a class="nav-link toc-link" href="#section-5">5. Comment Policy</a>
                                <a class="nav-link toc-link" href="#section-6">6. Intellectual Property</a>
                                <a class="nav-link toc-link" href="#section-7">7. Termination</a>
                                <a class="nav-link toc-link" href="#section-8">8. Limitation of Liability</a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-8">
                <span class="text-primary fw-semibold">Legal</span>
                <h1 class="h2 fw-bold mb-2">Terms of Service</h1>
                <p class="text-muted mb-5">Last updated: {{ now()->format('F j, Y') }}</p>

                <div class="mb-5">
                    <h2 id="section-1" class="h4 fw-bold">1. Acceptance of Terms</h2>
                    <p class="text-muted">By accessing or using BlogHub, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our platform. We reserve the right to modify these terms at any time.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-2" class="h4 fw-bold">2. User Accounts</h2>
                    <p class="text-muted">You are responsible for maintaining the confidentiality of your account credentials. You agree to provide accurate and complete information when creating an account. You must notify us immediately of any unauthorized use of your account.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-3" class="h4 fw-bold">3. Content Ownership</h2>
                    <p class="text-muted">You retain full ownership of the content you publish on BlogHub. By publishing, you grant us a non-exclusive, worldwide license to display, distribute, and promote your content on our platform. You may remove your content at any time.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-4" class="h4 fw-bold">4. Acceptable Use</h2>
                    <p class="text-muted">You agree not to use BlogHub for any unlawful purpose or to post any content that is defamatory, obscene, harassing, or otherwise objectionable. We reserve the right to remove any content that violates these terms.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-5" class="h4 fw-bold">5. Comment Policy</h2>
                    <p class="text-muted">Comments are welcome but must be respectful and relevant. We moderate comments and may remove those that are spam, offensive, or off-topic. Repeated violations may result in account suspension.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-6" class="h4 fw-bold">6. Intellectual Property</h2>
                    <p class="text-muted">BlogHub and its original content, features, and functionality are owned by BlogHub Pvt. Ltd. and are protected by international copyright, trademark, and other intellectual property laws.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-7" class="h4 fw-bold">7. Termination</h2>
                    <p class="text-muted">We may terminate or suspend your account at any time, without prior notice, for conduct that we believe violates these Terms of Service. Upon termination, your right to use the platform will immediately cease.</p>
                </div>

                <div class="mb-5">
                    <h2 id="section-8" class="h4 fw-bold">8. Limitation of Liability</h2>
                    <p class="text-muted">BlogHub is provided on an "as is" and "as available" basis. We do not warrant that the platform will be uninterrupted, error-free, or free of harmful components. To the fullest extent permitted by law, we disclaim all warranties, express or implied.</p>
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
