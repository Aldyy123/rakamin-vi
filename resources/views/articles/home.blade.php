@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Laravel 9 CRUD Post</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('articles.create') }}"> Create New Post</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Image</th>
            <th>Name</th>
            <th>Content</th>
            <th>Category</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($articles as $article)
            <tr>
                <td>{{ ++$i }}</td>
                <td class="text-center">
                    <img src="{{ Illuminate\Support\Facades\Storage::url($article->image) }}" class="img-thumbnail img-fluid" width="140px" alt="{{ $article->title }}">
                </td>
                <td>{{ $article->title }}</td>
                <td>{{ $article->content }}</td>
                <td>{{ $article->category->name }}</td>
                <td>
                    <form action="{{ route('articles.destroy', $article->id) }}" method="POST">

                        <a class="btn btn-info" href="{{ route('articles.show', $article->id) }}">Show</a>

                        <a class="btn btn-primary" href="{{ route('articles.edit', $article->id) }}">Edit</a>

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
