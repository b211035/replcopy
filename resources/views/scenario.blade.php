@extends('layouts.app')

@section('title', 'Scenario')

@section('content')
<a href="{{ route('dashbord') }}">戻る</a>

<div class="row">
    @foreach ($Scenario->Starts as $Cell)
        @include('cell', ['Cell' => $Cell])
    @endforeach
</div>
<div class="row">
    <form id="talkform" action="{{ route('addcell', $Scenario->id) }}" method="post">
        {{ csrf_field() }}
        <select name="prevcell_id" id="prevcell_id">
            <option value="" botid="">親</option>
            @foreach ($Scenario->Cells as $Cell)
                <option value="{{ $Cell->id }}"">{{ $Cell->id }}</option>
            @endforeach
        </select>

        <select name="system" id="system">
            <option value="" botid="">セルタイプ</option>
            <option value="0"">システム起点</option>
            <option value="1"">システム発話</option>
            <option value="2"">ユーザー起点</option>
            <option value="3"">ユーザー発話</option>
        </select>
        <input type="text" name="text" id="text">
        <input type="submit" name="" value="送信">
    </form>
</div>
@endsection