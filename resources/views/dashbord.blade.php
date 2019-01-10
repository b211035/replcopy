@extends('layouts.app')

@section('title', 'Dashbord')

@section('content')
@foreach ($Projects as $Project)
    <div class="menu">
        <ul>
            <li>
                <a href="{{ route('talktest') }}">会話テスト</a>
            </li>
        </ul>
    </div>
    <div class="Project">
        <p>{{ $Project->name }}</p>
        @foreach ($Project->Bots as $Bot)
        <div class="bot">
            <p>{{ $Bot->name }}</p>
            <ul>
            @foreach ($Bot->Scenarios as $Scenario)
                <li>
                    <a href="{{ route('scenario', $Scenario->id) }}">{{ $Scenario->name }}</a>
                </li>
            @endforeach
            </ul>
        </div>
        @endforeach
    </div>
@endforeach
@endsection