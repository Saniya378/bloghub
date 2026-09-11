  <footer class="bg-dark text-light pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold text-primary">BlogHub</h5>
                    <p class="text-secondary small">A modern blogging platform for readers and writers.</p>
                    <div>
                        <a href="#" class="text-light me-2"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light me-2"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-light me-2"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}" class="text-secondary text-decoration-none">Home</a></li>
                        <li><a href="{{ url('/about') }}" class="text-secondary text-decoration-none">About</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-secondary text-decoration-none">Contact</a></li>
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/categories') }}" class="text-secondary text-decoration-none">Technology</a></li>
                        <li><a href="{{ url('/categories') }}" class="text-secondary text-decoration-none">Travel</a></li>
                        <li><a href="{{ url('/categories') }}" class="text-secondary text-decoration-none">Food</a></li>
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold">Newsletter</h6>
                    <p class="text-secondary small">Get the latest updates</p>
                    <div class="input-group">
                        <input type="email" class="form-control form-control-sm" placeholder="Enter your email">
                        <button class="btn btn-primary btn-sm">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="text-secondary">
            <div class="d-flex justify-content-between flex-wrap">
                
                <p class="small mb-0">
                    <a href="{{ url('/terms') }}" class="text-secondary text-decoration-none me-3">Terms & Conditions</a>
                    <a href="{{ url('/privacy') }}" class="text-secondary text-decoration-none">Privacy Policy</a>
                </p>
            </div>
        </div>
    </footer>