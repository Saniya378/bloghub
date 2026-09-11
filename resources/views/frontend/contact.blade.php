@extends('frontend.layouts.app')

@section('title', 'BlogHub - Contact Us')

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5">

        <div class="row g-5">

            <!-- LEFT: Get in touch -->
            <div class="col-lg-5">
                <h2 class="fw-bold mb-2">Get In Touch</h2>
                <p class="text-secondary mb-4">We'd love to hear from you. Send us a message!</p>

                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-envelope fs-5 text-primary me-3"></i>
                    <div>
                        <p class="fw-semibold mb-0">Email</p>
                        <small class="text-secondary">hello@bloghub.com</small>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-3">
                    <i class="bi bi-telephone fs-5 text-primary me-3"></i>
                    <div>
                        <p class="fw-semibold mb-0">Phone</p>
                        <small class="text-secondary">+92 300 1234567</small>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <i class="bi bi-geo-alt fs-5 text-primary me-3"></i>
                    <div>
                        <p class="fw-semibold mb-0">Address</p>
                        <small class="text-secondary">123 BlogHub Street, Lahore, Pakistan</small>
                    </div>
                </div>

                <div>
                    <i class="bi bi-facebook fs-4 text-primary me-3"></i>
                    <i class="bi bi-twitter fs-4 text-primary me-3"></i>
                    <i class="bi bi-linkedin fs-4 text-primary me-3"></i>
                    <i class="bi bi-instagram fs-4 text-primary"></i>
                </div>
            </div>

            <!-- RIGHT: Contact form -->
            <div class="col-lg-7">
                <form>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Your Name</label>
                            <input type="text" class="form-control" placeholder="Your Name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Your Email</label>
                            <input type="email" class="form-control" placeholder="Your Email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" placeholder="Subject">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Your Message</label>
                        <textarea class="form-control" rows="5" placeholder="Your Message"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">Send Message</button>
                </form>
            </div>

        </div>

    </section>

@endsection