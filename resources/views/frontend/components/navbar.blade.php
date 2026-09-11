 <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary fs-4" href="{{ url('/') }}">BlogHub</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/categories') }}">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/trending') }}">Trending</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">Contact</a></li>
                </ul>

                <div class="d-flex align-items-center">
                    <a href="{{ url('/search') }}" class="text-dark me-3 fs-5"><i class="bi bi-search"></i></a>
                    <a href="{{ url('/login') }}" class="btn btn-outline-primary me-2">Login</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>