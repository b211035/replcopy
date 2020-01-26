@extends('layouts.app')

@section('title', 'Dashbord')

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/dictionary.css') }}">
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
      <form id="form" method="POST" action="@if (!empty($Dictionary->id)) {{ route('update_dictionary', $Dictionary->id) }} @else {{ route('save_dictionary') }} @endif">
        @csrf
        <input type="text" name="name" value="{{ $Dictionary->name }}">
        <textarea name="words">{{ $Dictionary->word_texts() }}</textarea>
        <button type="submit" id="submit">
          保存
        </button>
      </form>
    </div>
  </div>
@endsection