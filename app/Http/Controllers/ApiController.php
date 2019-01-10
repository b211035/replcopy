<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models as Models;

class ApiController extends Controller
{
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
        $appRecvTime = $request->input('appRecvTime');
        $appSendTime = $request->input('appSendTime');

        $User = Models\User::where('key', $appUserId)->first();
        $Bot = Models\Bot::where('key', $botId)->first();
        $Scenario = Models\Scenario::where('key', $initTopicId)->first();
        $Progress = $User->Progress->where('scenario_id', $Scenario->id)->first();

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
            $Progress->user_id = $User->id;
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
            $expression .= $NextCell->Speeches->random()->text;
            $Progress->scenario_cell_id = $NextCell->id;
            $Progress->save();
        }

        // 次のセルがユーザー発話か存在しなくなるまで進める
        while (!is_null($NextCell) && !$NextCell->hasNextUserCell()) {
            // 次のセルが
            $NextCell = $this->NextCell($NextCell->NextCells, $voiceText);
            if ($NextCell && $NextCell->system == 1) {
                $expression .= $NextCell->Speeches->random()->text;
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

    public function NextCell($NextCells, $voiceText) {
        foreach ($NextCells as $NextCell) {
            switch ($NextCell->system) {
                case 1:
                    if ($NextCell->Conditions->count() == 0) {
                        return $NextCell;
                    }
                    // 次のセルがシステム発話
                    foreach ($NextCell->Conditions as $Condition) {
                        // TODO ここで条件とマッチの検証
                        if (true) {
                            return $NextCell;
                        }
                    }
                    break;
                case 3:
                    // 次のセルがユーザー発話
                    foreach ($NextCell->Speeches as $Speech) {
                        // TODO ここで条件とマッチの検証
                        if (true) {
                            return $NextCell;
                        }
                    }
                    break;
            }
        }
        return null;
    }
}
