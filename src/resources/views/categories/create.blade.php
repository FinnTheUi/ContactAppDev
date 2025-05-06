@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Create New Category</h4>
                </div>

                <div class="card-body">
                    <!-- Category Form -->
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="type">Category Type</label>
                            <select name="type" class="form-control">
                                <option value="business">Business</option>
                                <option value="personal">Personal</option>
                            </select>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-success">Create Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
