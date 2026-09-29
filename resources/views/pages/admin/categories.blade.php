@extends('layouts.dashboard')

@section('title', 'Manage Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Manage Categories</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal"><i class="fa-solid fa-plus me-2"></i>Add Category</button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Articles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td><i class="fa-solid {{ $category->icon }} fa-lg text-primary"></i></td>
                    <td class="fw-semibold">{{ $category->name }}</td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td>{{ Str::limit($category->description, 50) }}</td>
                    <td>{{ $category->articles_count ?? 0 }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#categoryModal{{ $category->id }}"><i class="fa-solid fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $category->id }}"><i class="fa-solid fa-trash"></i></button>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="categoryModal{{ $category->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ url('/admin/categories/' . $category->id) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Category</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Slug</label>
                                                <input type="text" name="slug" class="form-control" value="{{ $category->slug }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Icon (Font Awesome class)</label>
                                                <input type="text" name="icon" class="form-control" value="{{ $category->icon }}" placeholder="fa-microchip">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
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
                        <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Category</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">Are you sure you want to delete "<strong>{{ $category->name }}</strong>"?</div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" action="{{ url('/admin/categories/' . $category->id . '/delete') }}">
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
                    <td colspan="6" class="text-center py-5 text-muted">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ url('/admin/categories') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Font Awesome class)</label>
                        <input type="text" name="icon" class="form-control" placeholder="fa-microchip">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
