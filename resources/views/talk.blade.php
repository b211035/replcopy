@extends('layouts.app')

@section('title', 'Talk')

@section('content')
    <a href="{{ route('dashbord') }}">戻る</a>

    <form id="userform" action="{{ route('registration_api') }}" method="post">
    </form>
    <form id="talkform" action="{{ route('dialogue_api') }}" method="post">
    </form>

    <select name="initTopicId" id="initTopicId">
        <option value="" botid="">シナリオ選択</option>
        @foreach ($Scenarios as $Scenario)
            <option value="{{ $Scenario->key }}" botid="{{ $Scenario->Bot->key }}">{{ $Scenario->name }}</option>
        @endforeach
    </select>
    <input type="hidden" name="botId" id="botId">
    <input type="text" name="voiceText" id="voiceText">
    <input type="hidden" name="appUserId" id="appUserId">
    <button type="button" id="talk">送信</button>

    <div id="talkerea"></div>

    <script src="{{ asset('/js/talktest.js') }}"></script>
@endsection