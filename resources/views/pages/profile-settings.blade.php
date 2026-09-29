@extends('layouts.app')

@section('title', 'Profile Settings — BlogHub')
@section('meta_description', 'Manage your BlogHub profile, update your bio, change your password, and customize your author presence.')

@section('content')
<section class="bh-profile-settings bh-reveal">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bh-section-header with-line mb-5">
                    <span class="bh-section-kicker">Account</span>
                    <h1 class="bh-h1 mb-2">Profile Settings</h1>
                    <p class="bh-body" style="color: var(--color-text-muted);">Manage your profile information, password, and author presence.</p>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Profile Information -->
                <div class="bh-card mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h5 mb-4">Profile Information</h2>
                        <form method="POST" action="{{ route('profile') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="tagline" class="form-label">Tagline</label>
                                    <input type="text" class="form-control @error('tagline') is-invalid @endif" id="tagline" name="tagline" value="{{ old('tagline', $user->tagline) }}">
                                    @error('tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @endif" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <label for="specialty" class="form-label">Specialty</label>
                                <input type="text" class="form-control @error('specialty') is-invalid @endif" id="specialty" name="specialty" value="{{ old('specialty', $user->specialty) }}">
                                @error('specialty')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <label for="avatar" class="form-label">Avatar</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @endif" id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($user->avatar)
                                <div class="mt-2">
                                    <img src="{{ $user->avatar }}" alt="Current avatar" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                                @endif
                            </div>

                            <h3 class="h6 mt-4 mb-3">Social Links</h3>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="social_twitter" class="form-label">Twitter</label>
                                    <input type="url" class="form-control" id="social_twitter" name="social[twitter]" value="{{ old('social.twitter', $user->social['twitter'] ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="social_github" class="form-label">GitHub</label>
                                    <input type="url" class="form-control" id="social_github" name="social[github]" value="{{ old('social.github', $user->social['github'] ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="social_linkedin" class="form-label">LinkedIn</label>
                                    <input type="url" class="form-control" id="social_linkedin" name="social[linkedin]" value="{{ old('social.linkedin', $user->social['linkedin'] ?? '') }}">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="bh-card mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h5 mb-4">Change Password</h2>
                        <form method="POST" action="{{ url('/profile/password') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @endif" id="current_password" name="current_password" required>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @endif" id="new_password" name="new_password" required>
                                    @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Become an Author (readers only) -->
                @if($user->role === 'reader')
                <div class="bh-card">
                    <div class="card-body p-4 p-md-5 text-center">
                        <h2 class="h5 mb-3">Become an Author</h2>
                        <p class="text-muted mb-4">Share your expertise with our community. Upgrade to an author account to publish articles and build your audience.</p>
                        <form method="POST" action="{{ url('/become-author') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">Upgrade to Author</button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.initScrollReveal) window.initScrollReveal();
});
</script>
@endpush
