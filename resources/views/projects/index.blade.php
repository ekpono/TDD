@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="flex items-center">
            <h2 class="mr-auto">Billboard</h2>
            <a style="margin: auto" href="/projects/create">New Project</a>
        </div>
        @forelse($projects as $project)
            <li><a href="{{$project->path()}}">{{$project->title}}</a></li>

        @empty
            <li>No projects yet</li>
        @endforelse

    </div>
@endsection
