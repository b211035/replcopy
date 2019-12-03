@extends('layouts.app')

@section('title', 'Dashbord')

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/index.css') }}">
@endsection
@section('script')
  <script src="{{ asset('/js/index.js') }}"></script>
@endsection

@section('content')
  <div id="main">
    <div id="sideber" class="col-2">
      <ul>
        <li>プロジェクト一覧</li>
          @foreach ($Projects as $Project)
            <p>{{ $Project->name }}</p>
          @endforeach
        <li>マイページ</li>
        <li>使い方</li>
        <li>リファレンス</li>
      </ul>
    </div>

    <div id="contents" class="col-10">
      <h2>ボット一覧</h2>
      <div id="bots">
      @foreach ($choiceProject->Bots as $Bot)
        <div class="">
          {{ $Bot->name }}
        </div>
        <div class="">
          ＋ボットを追加する
        </div>
      @endforeach
      </div>

      @foreach ($choiceProject->Bots as $Bot)
        <div class="bot">
          <div class="info">
            <div>名前:{{ $Bot->name }}</div>
            <div>ID:{{ $Bot->key }}</div>
            <div>APIキー:{{ $choiceProject->api_key }}</div>
          </div>

          <div class="manage">
            <div>コンソール</div>
            <div>辞書</div>
            <div>変数</div>
          </div>
          <div class="seanario">
            <div class="row title">
              <div class="col name">シナリオ名(シナリオID)</div>
              <div class="col type">タイプ</div>
              <div class="col status">ステータス</div>
              <div class="col manage"></div>
              <div class="col del"></div>
            </div>

            @foreach ($Bot->Scenarios as $Scenario)
              <div class="row contents">
                <div class="col name">
                  <a href="{{ route('scenario', $Scenario->id) }}">{{ $Scenario->name }}</a>({{ $Scenario->key }})
                </div>
                <div class="col type">タイプ</div>
                <div class="col status">ステータス</div>
                <div class="col manage"></div>
                <div class="col del"></div>
              </div>
            @endforeach
            <div class="row">
              <div id="add_scenario">
                <a href="{{ route('new_scenario') }}">
                  ＋シナリオを追加する
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endsection