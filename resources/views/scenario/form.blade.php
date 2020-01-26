@extends('layouts.app')

@section('title', 'Scenario')

@section('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/jquery-ui.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/css/scenario.css') }}">
@endsection
@section('script')
  <script src="{{ asset('/js/scenario.js') }}"></script>
@endsection

@section('content')
  <div id="main">
    <div id="sideber">
      <div data-type="u_t">ユーザー発話</div>
      <div data-type="s_t">システム発話</div>
      <div data-type="s_m">シナリオ遷移</div>
      <div data-type="u_s">ユーザー起点</div>
      <div data-type="s_s">システム起点</div>
    </div>

    <div id="contents">
      <!-- <div id="active_area"></div> -->
      <svg id="active_area"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
      @if (!empty($Scenario->svg))
        {!! $Scenario->svg !!}
      @else
        <line id="arrow" x1="0" x2="0" y1="0" y2="0" stroke-width="1" stroke="black"></line>
      @endif
      </svg>
    </div>

    <div id="detail">
      <div id="detail_change">
        <div id="search" class="open" style="display: none;"><!-- TODO -->
          <h3>ボックス検索</h3>
          <input type="text" name="search">

          <h3>絞り込み</h3>
          <span><input type="checkbox" name="filter">ユーザー発話</span>
          <span><input type="checkbox" name="filter">システム発話</span>
          <span><input type="checkbox" name="filter">変数</span>

          <div id="search_results"></div>
        </div>
        <div id="input">
          <form id="form" method="POST" action="@if (!empty($Scenario->id)) {{ route('update_scenario', $Scenario->id) }} @else {{ route('save_scenario') }} @endif">
            @csrf
            <div>
              <textarea  name="svg" id="svg_inner" style="display: none;">
              </textarea>
              <input type="text" id="scenario_name" name="name" placeholder="シナリオ名" value="{{ $Scenario->name }}">
            </div>

            @if (!empty($Scenario->Cells))
              <div id="cells" data-index="{{ $Scenario->Cells->max('svg_index') +1 }}">
              @foreach ($Scenario->Cells as $Cell)
                <div data-index="{{ $Cell->svg_index }}">
                  <input type="hidden" name="cells[{{ $Cell->svg_index }}][system]" value="{{ $Cell->system }}">
                  <input type="hidden" name="cells[{{ $Cell->svg_index }}][svg_index]" value="{{ $Cell->svg_index }}">
                  <div class="input_tabs">
                    <div class="condition">入力条件</div>
                    <div class="speech">テキスト</div>
                    <div class="memory">覚える内容</div>
                  </div>
                  <div class="input_boxs">
                    <div class="condition">
                      覚えているキーワードで発話する条件を設定します
                      <div class="conditions" data-index="{{ count($Cell->Conditions) }}">
                      @foreach ($Cell->Conditions as $Condition)
                        <div class="box conditions">
                          <span>@<input type="text" name="cells[{{ $Cell->svg_index }}][condition][{{ $loop->index }}][variable]" value="{{ $Condition->variable->name }}"></span>
                          <input type="hidden" class="condition_type_val" name="cells[{{ $Cell->svg_index }}][condition][{{ $loop->index }}][condition]" value="{{ $Condition->condition }}">
                          <input type="hidden" class="condition_value_val" name="cells[{{ $Cell->svg_index }}][condition][{{ $loop->index }}][condition_value]" value="{{ $Condition->condition_value }}">

                          <span>が</span>
                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][condition][{{ $loop->index }}][c]" value="1" @if ($Condition->condition == 1) checked="checked" @endif>設定されているとき</span>
                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][condition][{{ $loop->index }}][c]" value="2" @if ($Condition->condition == 2) checked="checked" @endif>設定されていないとき</span>
                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][condition][{{ $loop->index }}][c]" value="3" @if ($Condition->condition == 3) checked="checked" @endif>テキスト判定</span>
                          <span><input type="text" class="condition_value" @if ($Condition->condition == 3) value="{{ $Condition->condition_value }}" @endif>のとき</span>
                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][condition][{{ $loop->index }}][c]" value="4" @if ($Condition->condition == 4) checked="checked" @endif>数式判定</span>
                          <span><input type="text" class="condition_value" @if ($Condition->condition == 4) value="{{ $Condition->condition_value }}" @endif>のとき</span>
                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][condition][{{ $loop->index }}][c]" value="5" @if ($Condition->condition == 5) checked="checked" @endif>正規表現</span>
                          <span><input type="text" class="condition_value" @if ($Condition->condition == 5) value="{{ $Condition->condition_value }}" @endif>のとき</span>
                        </div>
                      @endforeach
                      </div>
                      <button class="conditions_add">＋条件を追加する</button>
                    </div>
                    <div class="speech target">
                      ボットの発話内容を設定します<br>
                      #*; 直前のユーザー発話の*の値に置き換えます<br>
                      #all; 直前のユーザー発話の内容に置き換えます<br>
                      @XXX; 変数XXXの値に置き換えます <br>
                      !XXX; IDXXXの辞書とマッチさせます <br>
                      <div class="speechs" data-index="{{ count($Cell->Speeches) }}">
                      @foreach ($Cell->Speeches as $Speech)
                        <div class="box speechs">
                          <input type="text" name="cells[{{ $Cell->svg_index }}][speech][{{ $loop->index }}][text]" value="{{ $Speech->text }}">
                          <input type="hidden" name="cells[{{ $Cell->svg_index }}][speech][{{ $loop->index }}][condition]" value="{{ $Speech->condition }}">
                          <select  class="condition_value" style="display: none;">
                            <option value="1" @if ($Speech->condition == 1) checked="checked" @endif>完全一致</option>
                            <option value="2" @if ($Speech->condition == 2) checked="checked" @endif>正規表現</option>
                          </select>
                        </div>
                      @endforeach
                      </div>
                      <button class="speechs_add">＋条件を追加する</button>
                    </div>
                    <div class="memory">
                      ボットに直前のユーザの発話内容や、 情報を覚えさせることで、発話やシナリオの分岐に利用できます
                      <div class="memories" data-index="{{ count($Cell->Memorys) }}">
                      @foreach ($Cell->Memorys as $Memory)
                        <div class="box memories">
                          <span>@<input type="text" name="cells[{{ $Cell->svg_index }}][memory][{{ $loop->index }}][variable]" value="{{ $Memory->variable->name }}"></span>
                          <input type="hidden" class="condition_type_val" name="cells[{{ $Cell->svg_index }}][memory][{{ $loop->index }}][condition]" value="{{ $Memory->condition }}">
                          <input type="hidden" class="condition_value_val" name="cells[{{ $Cell->svg_index }}][memory][{{ $loop->index }}][condition_value]" value="{{ $Memory->condition_value }}">

                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][memory][{{ $loop->index }}][c]" value="1" @if ($Memory->condition == 1) checked="checked" @endif>テキストで設定</span>
                          <span><input type="text" class="condition_value"></span>
                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][memory][{{ $loop->index }}][c]" value="2" @if ($Memory->condition == 2) checked="checked" @endif>直前のユーザー発話から抽出
                            <select  class="condition_value">
                              <option value="1">アスタリスク</option>
                              <option value="2">ユーザー発話全体</option>
                            </select>
                          </span>
                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][memory][{{ $loop->index }}][c]" value="3" @if ($Memory->condition == 3) checked="checked" @endif>AIMLで記述する</span>
                          <span><input type="text" class="condition_value"></span>
                          <span><input type="radio" class="condition_type" name="cells[{{ $Cell->svg_index }}][memory][{{ $loop->index }}][c]" value="4" @if ($Memory->condition == 4) checked="checked" @endif>覚えている内容を忘れさせる</span>
                        </div>
                      @endforeach
                      </div>
                      <button class="memories_add">＋条件を追加する</button>
                    </div>
                  </div>
                </div>
              @endforeach
              </div>
              <div id="chains" data-index="{{ array_key_last($CellChains) +1 }}">
              @foreach ($CellChains as $CellChain)
                <div data-index="{{ $CellChain->svg_index }}">
                  <input type="hidden" name="chains[{{ $CellChain->svg_index }}][svg_index]" value="{{ $CellChain->prevCell->svg_index }}">
                  <input type="hidden" name="chains[{{ $CellChain->svg_index }}][prev]" value="{{ $CellChain->prevCell->svg_index }}">
                  <input type="hidden" name="chains[{{ $CellChain->svg_index }}][next]" value="{{ $CellChain->nextCell->svg_index }}">
                </div>
              @endforeach
              </div>
            @else
              <div id="cells" data-index="0">
              </div>
              <div id="chains" data-index="0">
              </div>
            @endif
          </form>
        </div>
      </div>
      <div id="manage">
        <div id="save">
          <button type="button" id="submit">
            保存
          </button>
        </div>
      </div>
    </div>
  </div>

<div id="chain_proto" data-index="">
  <input type="hidden" name="[svg_index]">
  <input type="hidden" name="[prev]">
  <input type="hidden" name="[next]">
</div>

<div id="proto_types" data-index="">
  <input type="hidden" name="[system]">
  <input type="hidden" name="[svg_index]">
  <div class="input_tabs">
    <div class="condition">入力条件</div>
    <div class="speech">テキスト</div>
    <div class="memory">覚える内容</div>
  </div>
  <div class="input_boxs">
    <div class="condition">
      覚えているキーワードで発話する条件を設定します
      <div class="conditions" data-index="0">
      </div>
      <button class="conditions_add">＋条件を追加する</button>
    </div>
    <div class="speech target">
      ボットの発話内容を設定します<br>
      #*; 直前のユーザー発話の*の値に置き換えます<br>
      #all; 直前のユーザー発話の内容に置き換えます<br>
      @XXX; 変数XXXの値に置き換えます <br>
      !XXX; IDXXXの辞書とマッチさせます <br>
      <div class="speechs" data-index="1">
        <div class="box">
          <input type="text" name="[speech][0][text]">
          <input type="hidden" name="[speech][0][condition]">
          <select  class="condition_value" style="display: none;">
            <option value="1">完全一致</option>
            <option value="2">正規表現</option>
          </select>
        </div>
      </div>
      <button class="speechs_add">＋条件を追加する</button>
    </div>
    <div class="memory">
      ボットに直前のユーザの発話内容や、 情報を覚えさせることで、発話やシナリオの分岐に利用できます
      <div class="memories" data-index="0">
      </div>
      <button class="memories_add">＋条件を追加する</button>
    </div>
  </div>
</div>


<div id="input_proto">
  <div class="box conditions">
    <span>@<input type="text" name="[condition][_index][variable]"></span>
    <input type="hidden" class="condition_type_val" name="[condition][_index][condition]">
    <input type="hidden" class="condition_value_val" name="[condition][_index][condition_value]">

    <span>が</span>
    <span><input type="radio" class="condition_type" name="[condition][_index][c]" value="1">設定されているとき</span>
    <span><input type="radio" class="condition_type" name="[condition][_index][c]" value="2">設定されていないとき</span>
    <span><input type="radio" class="condition_type" name="[condition][_index][c]" value="3">テキスト判定</span>
    <span><input type="text" class="condition_value">のとき</span>
    <span><input type="radio" class="condition_type" name="[condition][_index][c]" value="4">数式判定</span>
    <span><input type="text" class="condition_value">のとき</span>
    <span><input type="radio" class="condition_type" name="[condition][_index][c]" value="5">正規表現</span>
    <span><input type="text" class="condition_value">のとき</span>
  </div>

  <div class="box speechs">
    <input type="text" name="[speech][_index][text]">
    <input type="hidden" name="[speech][_index][condition]">
    <select  class="condition_value" style="display: none;">
      <option value="1">完全一致</option>
      <option value="2">正規表現</option>
    </select>
  </div>

  <div class="box memories">
    <span>@<input type="text" name="[memory][_index][variable]"></span>
    <input type="hidden" class="condition_type_val" name="[memory][_index][condition]">
    <input type="hidden" class="condition_value_val" name="[memory][_index][condition_value]">

    <span><input type="radio" class="condition_type" name="[memory][_index][c]" value="1">テキストで設定</span>
    <span><input type="text" class="condition_value"></span>
    <span><input type="radio" class="condition_type" name="[memory][_index][c]" value="2">直前のユーザー発話から抽出
      <select  class="condition_value">
        <option value="1">アスタリスク</option>
        <option value="2">ユーザー発話全体</option>
      </select>
    </span>
    <!-- <span><input type="radio" class="condition_type" name="[memory][_index][c]" value="3">AIMLで記述する</span> -->
    <!-- <span><input type="text" class="condition_value"></span> -->
    <span><input type="radio" class="condition_type" name="[memory][_index][c]" value="4">覚えている内容を忘れさせる</span>
  </div>
</div>

<div id="move_proto">
  <input type="hidden" name="[system]">
  <input type="hidden" name="[svg_index]">
  <div class="">
    <div class="">
      <input type="hidden" name="[speech][_index][text]" value="move">
      <select class="move_scenario" name="[speech][_index][condition]">
        <option>-----------------</option>
        @foreach ($AScenarios as $AScenario)
          <option value="{{ $AScenario->id }}" data-name="{{ $AScenario->name }}">{{ $AScenario->name }}</option>
        @endforeach
      </select>
    </div>
  </div>
</div>

@endsection
