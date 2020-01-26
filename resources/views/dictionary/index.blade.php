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
            <a href="{{ route('dashbord') }}">{{ $Project->name }}</a>
          @endforeach
        <li>マイページ</li>
        <li>使い方</li>
        <li>リファレンス</li>
      </ul>
    </div>

    <div id="contents" class="col-10">
      <h2>変数一覧</h2>

      <div class="dictionary">
        <div class="row title">
          <div class="col name">辞書名(ID)</div>
          <div class="col del"></div>
        </div>

        @foreach ($Bot->Dictionaries as $Dictionary)
          <div class="row contents">
            <div class="col name">
              <a href="{{ route('edit_dictionary', $Dictionary->id) }}">{{ $Dictionary->name }}</a>({{ $Dictionary->id }})
            </div>
            <div class="col del"></div>
          </div>
        @endforeach
        <div class="row">
          <div id="add_dictionary">
            <a href="{{ route('new_dictionary') }}">
              ＋辞書を追加する
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection