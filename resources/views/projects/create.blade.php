@extends('layouts.app')
@section('content')

    <div class="container">
        <h2>Create a project</h2>
        <form action="/projects" method="post">
            @csrf
            <div class="form-group">
                <label for="exampleInputEmail1">Title</label>
                <input type="Title" name="title" class="form-control" id="exampleInputEmail1" aria-describedby="TitleHelp" placeholder="Enter Title">
            </div>
            <div class="form-group">
                <label for="exampleFormControlTextarea1">Description</label>
                <textarea name="description" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="/projects" class="btn btn-primary">Cancel</a>
        </form>
    </div>
@endsection
