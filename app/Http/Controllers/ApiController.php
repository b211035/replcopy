<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models as Models;

class ApiController extends Controller
{
  var $User;
  var $Bot;
  var $Scenario;
  var $Progress;
  var $Voice = ['1' => '', '2' => ''];

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest');
  }

  public function registration(Request $request)
  {
    // TODO ヘッダ管理
    $result = [];
    $botId = $request->input('botId');

    $Bot = Models\Bot::where('key', $botId)->first();
    if ($Bot) {
      $id = bin2hex(random_bytes(32));
      $userid = implode('-', [substr($id,0,8), substr($id,8,4), substr($id,12,4), substr($id,16,4), substr($id,20,12)]);

      $User = Models\User::create([
        'bot_id' => $Bot->id,
        'name' => $userid,
        'key' => $userid
      ]);
      $result['appUserId'] = $userid;
    } else {
      // TODO エラー
    }

    return response()->json($result);
  }

  public function dialogue(Request $request)
  {
    $result = [];
    $expression = '';

    $appUserId = $request->input('appUserId');
    $botId     = $request->input('botId');
    $voiceText = $request->input('voiceText');
    $initTalkingFlag = $request->input('initTalkingFlag');
    $initTopicId = $request->input('initTopicId');
    // $appRecvTime = $request->input('appRecvTime');
    // $appSendTime = $request->input('appSendTime');

    $this->User = Models\User::where('key', $appUserId)->first();
    $this->Bot = Models\Bot::where('key', $botId)->first();
    $Scenario = Models\Scenario::where('key', $initTopicId)->first();
    $Progress = $this->User->Progress->where('scenario_id', $Scenario->id)->first();

    // 初期化の状態ならシナリオのスタートを検証
    if ($initTalkingFlag) {
      if (!$Scenario) {
        // シナリオがないならエラーにする
        // TODO error
        return;
      }

      // 現在のシナリオの進捗を破棄して新規登録
      if ($Progress) {
        $Progress->delete();
      }
      $Progress = new Models\Progress;
      $Progress->user_id = $this->User->id;
      $Progress->scenario_id = $Scenario->id;

      if (empty($voiceText) || $voiceText == 'init') {
        // 取得文言なしならシステム起点の次を評価
        $NextCells = $Scenario->SystemStarts->first()->NextCells;
      } else {
        // それ以外ならユーザー起点の次セルの評価に移る
        $NextCells = $Scenario->UserStarts;
      }
    } else {
      // 初期化なしなら進捗の次セルを評価
      $NextCells = $Progress->Cell->NextCells;
    }
    $NextCell = $this->NextCell($NextCells, $voiceText);


    if (is_null($NextCell)) {
      // 次のセルがなかったらNO MATCH
      $date = new \DateTime();
      $result = [
        'systemText' => [
          'expression'=> 'NO MATCH',
          'utterance'=> 'NO MATCH'
        ],
        'serverSendTime' => $date->format('Y-m-d H:i:a')
      ];
      return response()->json($result);
    }

    // 発話を取得
    if ($NextCell->system == 1) {
      $expression .= $this->replaceSpeech($NextCell);
      $Progress->scenario_cell_id = $NextCell->id;
      $Progress->save();
    }

    // 次のセルがユーザー発話か、存在しなくなるまで進める
    while (!is_null($NextCell) && !$NextCell->hasNextUserCell()) {
      // 次のセルを取得
      $NextCell = $this->NextCell($NextCell->NextCells, $voiceText);
      if ($NextCell && $NextCell->system == 1) {
        $expression .= $this->replaceSpeech($NextCell);
        $Progress->scenario_cell_id = $NextCell->id;
        $Progress->save();
      }
    }
    // 次のセルがなかったらNO MATCH
    $date = new \DateTime();
    $result = [
      'systemText' => [
        'expression'=> $expression,
        'utterance'=> $expression
      ],
      'serverSendTime' => $date->format('Y-m-d H:i:a')
    ];
    return response()->json($result);
  }

  private function NextCell($NextCells, $voiceText) {
    $return = null;
    foreach ($NextCells as $NextCell) {
      switch ($NextCell->system) {
        case 1:
          if ($NextCell->Conditions->count() == 0) {
            // return $NextCell;
            $return = $NextCell;
            break 2;
          }
          // 次のセルがシステム発話
          foreach ($NextCell->Conditions as $Condition) {
            if ($this->matchCondition($Condition)) {
              // return $NextCell;
              $return = $NextCell;
              break 3;
            }
          }
          break;
        case 3:
          // 次のセルがユーザー発話
          foreach ($NextCell->Speeches as $Speech) {
            if ($this->matchText($Speech, $voiceText)) {
              // return $NextCell;
              $return = $NextCell;
              break 3;
            }
          }
          break;
      }
    }
    if ($return) {
      $this->setVariable($return, $voiceText);
    }
    return $return;
  }

  private function matchCondition($Condition){
    $UserStorages = $Condition->Variable->Storages->where('user_id', $this->User->id)->first();
    switch ($Condition->condition) {
      case 1:
        // 変数が記憶されているとき
        return !empty($UserStorages);
        break;
      case 2:
        // 変数が記憶されていないとき
        return empty($UserStorages);
        break;
      case 3:
        // 変数が文字列と同等のとき
        if (empty($UserStorages)) {
          return false;
        }
        return ($UserStorages->value == $Condition->condition_value);
        break;
      case 4:
        // 変数計算式通りの時
        if (empty($UserStorages)) {
          return false;
        }
        // TODO 整理
        return ($UserStorages->value == $Condition->condition_value);
        break;
      case 5:
        // 変数が正規表現通りのとき
        if (empty($UserStorages)) {
          return false;
        }
        // TODO 整理
        return preg_match($Condition->condition_value, $UserStorages->value);
        break;
    }
  }

  private function matchText($Speech, $voice){
    $this->Voice['2'] = $voice;
    if (strrpos($Speech->text, '*') === false) {
      // アスタリスクなしなら厳密チェック
      return ($Speech->text == $voice);
    } else {
      // アスタリスクはワイルドカード
      $pattern = '/'. str_replace('*', '(.+)', $Speech->text) .'/';
      $result = preg_match($pattern, $voice, $m);
      if ($result !== false) {
        $this->Voice['1'] = $m['1'];
      }
      return $result;
    }
  }

  private function setVariable($Cell, $voiceText){
    foreach ($Cell->Memorys as $Memory) {
      $UserStorages = $Memory->Variable->Storages->where('user_id', $this->User->id)->first();
      if (empty($UserStorages)) {
        if ($Memory->condition == 4) {
          return;
        }
        $UserStorages = new Models\Storage;
        $UserStorages->user_id = $this->User->id;
      }

      switch ($Memory->condition) {
        case 1:
          $UserStorages->value = $Memory->condition_value;
          $Memory->Variable->Storages()->save($UserStorages);
          break;
        case 2:
          $UserStorages->value = $this->Voice[$Memory->condition_value];
          $Memory->Variable->Storages()->save($UserStorages);
          break;
        case 4:
          if ($UserStorages) {
            $UserStorages->delete();
          }
          break;
      }
    }
  }

  private function replaceSpeech($NextCell){
    $speech_text = $NextCell->Speeches->random()->text;
    $pattern = '/@(.+);/U';

    $result = preg_match_all($pattern, $speech_text, $matches);
    foreach ($matches[0] as $key => $matche) {
      $Variable = $this->Bot->Variables->where('name', $matches[1][$key])->first();
      if ($Variable) {
        $UserStorages = $Variable->Storages->where('user_id', $this->User->id)->first();
        if ($UserStorages) {
          $speech_text = str_replace($matche, $UserStorages->value, $speech_text);
        }
      }
    }

    $speech_text = str_replace('#*;', $this->Voice[1], $speech_text);
    $speech_text = str_replace('#all;', $this->Voice[2], $speech_text);

    return $speech_text;
  }
}
