@extends('frontend.layouts.app')

@section('title', 'BlogHub - ' . $categoryName)

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5">

        <h2 class="fw-bold mb-1">Category: {{ $categoryName }}</h2>
        <p class="text-secondary mb-5">{{ $posts->count() }} {{ Str::plural('article', $posts->count()) }} found</p>

        @if ($posts->count())
            <div class="row g-4 mb-4">
                @foreach ($posts as $post)
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 flex-row position-relative">
                            <a href="{{ url('/blog/' . $post['slug']) }}" class="stretched-link"></a>
                            <img src="{{ $post['thumb'] }}" class="rounded-start" style="width:160px;height:140px;object-fit:cover;" alt="{{ $post['title'] }}">
                            <div class="card-body">
                                <h6 class="card-title">{{ $post['title'] }}</h6>
                                <small class="text-secondary">{{ $post['date'] }} &bull; {{ $post['read_time'] }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-folder2-open fs-1 text-secondary mb-3 d-block"></i>
                <p class="text-secondary mb-4">Is category mein abhi koi article maujood nahi hai.</p>
                <a href="{{ url('/categories') }}" class="btn btn-primary px-4">Back to Categories</a>
            </div>
        @endif

    </section>

    @include('frontend.components.footer')

@endsection