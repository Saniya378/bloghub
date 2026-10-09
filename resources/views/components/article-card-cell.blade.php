{{--
    One article inside a responsive grid column.

    Used by the blogs page, the /api/posts listing and the live feed so all
    three produce identical markup (column classes + the data-article-id the
    live feed uses to avoid inserting duplicates).
--}}
<div class="col-md-6 col-lg-4" data-article-id="{{ $article->id }}">
    @include('components.article-card', ['article' => $article])
</div>