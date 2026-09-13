@extends('frontend.layouts.app')

@section('title', 'BlogHub - ' . $post['title'])

@section('content')
@include('frontend.components.navbar')

    <section class="container py-5">

        <div class="row g-5">

            <!-- LEFT: Article content -->
            <div class="col-lg-8">

                <span class="badge {{ $post['badge_color'] }} mb-3">{{ strtoupper($post['category']) }}</span>
                <h1 class="fw-bold mb-3">{{ $post['title'] }}</h1>

                <div class="d-flex align-items-center text-secondary small mb-4">
                    <a href="{{ url('/author') }}" class="text-decoration-none">By {{ $post['author'] }}</a>
                    <span class="mx-2">&bull;</span>
                    <span>{{ $post['date'] }}</span>
                    <span class="mx-2">&bull;</span>
                    <span>{{ $post['read_time'] }}</span>
                </div>

                <img src="{{ $post['image'] }}" class="img-fluid rounded-3 mb-4" alt="{{ $post['title'] }}">

                @foreach ($post['sections'] as $section)
                    @if (!empty($section['heading']))
                        <h4 class="fw-bold mb-3">{{ $section['heading'] }}</h4>
                    @endif
                    <p class="text-secondary mb-4">
                        {{ $section['text'] }}
                    </p>
                @endforeach

            </div>

            <!-- RIGHT: Sidebar -->
            <div class="col-lg-4">

                <div class="border rounded-3 p-4 mb-4">
                    <h6 class="fw-bold mb-3">Categories</h6>
                    <a href="{{ url('/category/' . \Illuminate\Support\Str::slug($post['category'])) }}" class="text-decoration-none">{{ $post['category'] }}</a>
                </div>

                <div class="border rounded-3 p-4 mb-4">
                    <h6 class="fw-bold mb-3">Tags</h6>
                    @foreach ($post['tags'] as $tag)
                        <a href="{{ url('/tag') }}" class="badge bg-light text-dark border me-2 mb-2 text-decoration-none">{{ $tag }}</a>
                    @endforeach
                </div>

                @if ($related->count())
                    <div class="border rounded-3 p-4">
                        <h6 class="fw-bold mb-3">Related Posts</h6>

                        @foreach ($related as $item)
                            <div class="d-flex {{ !$loop->last ? 'mb-3' : '' }} position-relative">
                                <a href="{{ url('/blog/' . $item['slug']) }}" class="stretched-link"></a>
                                <img src="{{ $item['thumb'] }}" class="rounded me-3" style="width:70px;height:60px;object-fit:cover;" alt="related">
                                <div>
                                    <p class="small fw-semibold mb-0">{{ $item['title'] }}</p>
                                    <small class="text-secondary">{{ $item['date'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>

    </section>

    @include('frontend.components.footer')

@endsection