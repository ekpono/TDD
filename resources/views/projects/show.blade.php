@extends('app')

@section('content')
    <h2>{{ $project->title }}</h2>

    <div>{{ $project->description }}</div>

    @foreach($project->tasks as $task)
    <div class="mb-3">{{$task->body}}</div>

    @endforeach
    <div class="mb-3">{{$project->notes}}</div>
@endsection
