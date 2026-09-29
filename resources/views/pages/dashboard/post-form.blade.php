@extends('layouts.dashboard')

@section('title', ($article ? 'Edit' : 'Create') . ' Post — Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">{{ $article ? 'Edit Post' : 'Create New Post' }}</h1>
    <a href="{{ url('/dashboard/posts') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
</div>

<form method="POST" action="{{ $article ? url('/dashboard/posts/' . $article->id) : url('/dashboard/posts') }}" enctype="multipart/form-data" id="postForm">
    @csrf
    @if($article) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Title -->
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg" value="{{ old('title', $article->title ?? '') }}" placeholder="Enter a compelling title..." required>
                    @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Slug -->
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $article->slug ?? '') }}" placeholder="auto-generated-from-title" required>
                    <small class="text-muted">Auto-generated from title. Edit if needed.</small>
                    @error('slug') <div class="text-danger small">{{ $message }}</div> @endif
                </div>
            </div>

            <!-- Excerpt -->
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">Excerpt</label>
                    <textarea name="excerpt" class="form-control" rows="3" maxlength="500" placeholder="Brief summary of your article...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                    <small class="text-muted">{{ 500 - strlen(old('excerpt', $article->excerpt ?? '')) }} characters remaining</small>
                    @error('excerpt') <div class="text-danger small">{{ $message }}</div> @endif
                </div>
            </div>

            <!-- Content Editor -->
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">Content</label>
                    <!-- Toolbar -->
                    <div class="btn-toolbar mb-2" role="toolbar">
                        <div class="btn-group btn-group-sm me-2">
                            <button type="button" class="btn btn-outline-secondary" data-action="bold" title="Bold"><i class="fa-solid fa-bold"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-action="italic" title="Italic"><i class="fa-solid fa-italic"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-action="heading" title="Heading"><i class="fa-solid fa-heading"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-action="link" title="Link"><i class="fa-solid fa-link"></i></button>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" data-action="quote" title="Quote"><i class="fa-solid fa-quote-left"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-action="code" title="Code"><i class="fa-solid fa-code"></i></button>
                            <button type="button" class="btn btn-outline-secondary" data-action="image" title="Image"><i class="fa-solid fa-image"></i></button>
                        </div>
                    </div>
                    <textarea name="content" id="content" class="form-control" rows="15" placeholder="Write your article content here... You can use HTML tags.">{{ old('content', $article->content ?? '') }}</textarea>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">Reading time: <span id="readingTime">{{ $article->reading_time ?? '0 min read' }}</span></small>
                        <small class="text-muted"><span id="wordCount">0</span> words</small>
                    </div>
                    @error('content') <div class="text-danger small">{{ $message }}</div> @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Publish Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Publish Settings</h5>
                </div>
                <div class="card-body">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="category_slug" class="form-select" required>
                        <option value="">Select category</option>
                        @foreach(['technology', 'education', 'travel', 'food', 'business', 'sports', 'health', 'lifestyle', 'finance', 'science', 'art', 'culture'] as $cat)
                        <option value="{{ $cat }}" {{ old('category_slug', $article->category_slug ?? '') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    @error('category_slug') <div class="text-danger small">{{ $message }}</div> @endif

                    <label class="form-label fw-semibold mt-3">Tags</label>
                    <input type="text" name="tags[]" class="form-control" value="{{ old('tags', isset($article) ? implode(', ', $article->tags ?? []) : '') }}" placeholder="Comma-separated tags">
                    <small class="text-muted">Separate tags with commas</small>

                    <label class="form-label fw-semibold mt-3">Status</label>
                    <div class="d-flex gap-2">
                        <button type="submit" name="status" value="draft" class="btn btn-outline-warning flex-fill"><i class="fa-solid fa-save me-1"></i> Save Draft</button>
                        <button type="submit" name="status" value="published" class="btn btn-success flex-fill"><i class="fa-solid fa-rocket me-1"></i> Publish</button>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Featured Image</h5>
                </div>
                <div class="card-body">
                    <input type="file" name="featured_image" class="form-control" accept="image/*" id="featuredImage">
                    @if($article && $article->image)
                    <div class="mt-3">
                        <img src="{{ $article->image }}" alt="" class="img-fluid rounded" style="max-height: 150px;">
                        <small class="text-muted d-block mt-1">Current image</small>
                    </div>
                    @endif
                    @error('featured_image') <div class="text-danger small">{{ $message }}</div> @endif
                </div>
            </div>
        </div>
    </div>
</form>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const contentInput = document.getElementById('content');
    const readingTimeEl = document.getElementById('readingTime');
    const wordCountEl = document.getElementById('wordCount');

    // Auto-generate slug
    titleInput.addEventListener('input', () => {
        if (!slugInput.dataset.edited) {
            slugInput.value = titleInput.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        }
    });
    slugInput.addEventListener('input', () => { slugInput.dataset.edited = '1'; });

    // Reading time & word count
    contentInput.addEventListener('input', () => {
        const text = contentInput.value;
        const words = text.trim().split(/\s+/).filter(w => w).length;
        wordCountEl.textContent = words;
        const minutes = Math.ceil(words / 200);
        readingTimeEl.textContent = minutes + ' min read';
    });
    // Trigger once for edit mode
    contentInput.dispatchEvent(new Event('input'));

    // Editor toolbar
    document.querySelectorAll('[data-action]').forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action;
            const start = contentInput.selectionStart;
            const end = contentInput.selectionEnd;
            const selected = contentInput.value.substring(start, end);

            let replacement = '';
            switch (action) {
                case 'bold': replacement = `<strong>${selected || 'bold text'}</strong>`; break;
                case 'italic': replacement = `<em>${selected || 'italic text'}</em>`; break;
                case 'heading': replacement = `<h2>${selected || 'Heading'}</h2>`; break;
                case 'link': replacement = `<a href="https://example.com">${selected || 'link text'}</a>`; break;
                case 'quote': replacement = `<blockquote><p>${selected || 'Quote'}</p></blockquote>`; break;
                case 'code': replacement = `<pre><code>${selected || 'code here'}</code></pre>`; break;
                case 'image': replacement = `<img src="https://picsum.photos/seed/${Date.now()}/800/400" alt="Image" class="img-fluid">`; break;
            }

            contentInput.value = contentInput.value.substring(0, start) + replacement + contentInput.value.substring(end);
            contentInput.focus();
            contentInput.dispatchEvent(new Event('input'));
        });
    });
});
</script>
@endpush
