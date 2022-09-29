@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Laravel 9 CRUD Categories</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('categories.create') }}"> Create New Categories</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success m-3">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($categories as $category)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $category->name }}</td>
                <td>
                    <form action="{{ route('categories.destroy', $category->id) }}" method="post">
                        <a class="btn btn-info" href="{{ route('categories.show', $category->id) }}">Show</a>

                        <a class="btn btn-primary" href="{{ route('categories.edit', $category->id) }}">Edit</a>

                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger delete-btn" value="{{ $category->id }}">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
