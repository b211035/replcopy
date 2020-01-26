@extends('layouts.app')

@section('title', 'Talk')

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/talk.css') }}">
@endsection
@section('script')
  <script src="{{ asset('/js/talk.js') }}"></script>
@endsection

@section('content')
  <div id="main">
    <div id="sideber" class="col-2">
      <ul>
        <li>プロジェクト一覧</li>
          @foreach ($Projects as $Project)
            <a href="{{ route('dashbord') }}">{{ $Project->name }}</a>
          @endforeach
        <li>マイページ</li>
        <li>使い方</li>
        <li>リファレンス</li>
      </ul>
    </div>

    <div id="contents" class="col-10">
        <form id="userform" action="{{ route('registration_api') }}" method="post">
        </form>
        <form id="groupform" action="{{ route('addgroup_api') }}" method="post">
        </form>
        <form id="talkform" action="{{ route('dialogue_api') }}" method="post">
        </form>

        <select name="initTopicId" id="initTopicId">
            <option value="" botid="">シナリオ選択</option>
            @foreach ($Scenarios as $Scenario)
                <option value="{{ $Scenario->key }}" botid="{{ $Scenario->Bot->key }}">{{ $Scenario->name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="appUserId" id="appUserId">
        <input type="hidden" name="botId" id="botId">
        <input type="hidden" name="groupId" id="groupId">
        <input type="text" name="voiceText" id="voiceText">
        <input type="checkbox" name="initTalkingFlag" id="initTalkingFlag"><label for="initTalkingFlag">初期化フラグ</label>
        <button type="button" id="talk">送信</button>

        <div id="talkerea"></div>

        <a href="{{ route('dashbord') }}">戻る</a>
    </div>
  </div>
@endsection

