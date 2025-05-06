@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Categories</h4>
                    <a href="{{ route('categories.create') }}" class="btn btn-success">Add Category</a>
                </div>

                <div class="card-body">
                    <!-- Categories Table -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ ucfirst($category->type) }}</td>
                                <td>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
