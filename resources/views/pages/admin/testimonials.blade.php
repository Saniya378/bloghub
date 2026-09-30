@extends('layouts.dashboard')

@section('title', 'Testimonials')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Testimonials</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testimonialModal"><i class="fa-solid fa-plus me-2"></i>Add Testimonial</button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Quote</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $testimonial)
                <tr>
                    <td><img src="{{ $testimonial->photo }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"></td>
                    <td class="fw-semibold">{{ $testimonial->name }}</td>
                    <td>{{ Str::limit($testimonial->quote, 60) }}</td>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                        <i class="{{ $i <= $testimonial->rating ? 'fa-solid fa-star text-warning' : 'fa-regular fa-star text-muted' }}"></i>
                        @endfor
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $testimonial->id }}"><i class="fa-solid fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $testimonial->id }}"><i class="fa-solid fa-trash"></i></button>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $testimonial->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ url('/admin/testimonials/' . $testimonial->id) }}" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Testimonial</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $testimonial->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Quote</label>
                                                <textarea name="quote" class="form-control" rows="3" required>{{ $testimonial->quote }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Rating (1-5)</label>
                                                <input type="number" name="rating" class="form-control" value="{{ $testimonial->rating }}" min="1" max="5" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Photo</label>
                                                <input type="file" name="photo" class="form-control" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal{{ $testimonial->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Testimonial</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">Are you sure you want to delete this testimonial?</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ url('/admin/testimonials/' . $testimonial->id . '/delete') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No testimonials found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($testimonials->hasPages())
    <div class="card-footer">{{ $testimonials->links() }}</div>
    @endif
</div>

<!-- Add Modal -->
<div class="modal fade" id="testimonialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ url('/admin/testimonials') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quote</label>
                        <textarea name="quote" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rating (1-5)</label>
                        <input type="number" name="rating" class="form-control" value="5" min="1" max="5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
