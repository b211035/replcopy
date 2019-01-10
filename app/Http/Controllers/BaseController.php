<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models as Models;

class BaseController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function dashbord(Request $request)
    {
        $Projects = Models\Project::all();

        return view('dashbord')
        ->with('Projects', $Projects);
    }
    public function dictionary(Request $request)
    {
        return view('welcome');
    }
    public function variable(Request $request)
    {
        return view('welcome');
    }
    public function scenario(Request $request, $id)
    {
        $Scenario = Models\Scenario::find($id);

        return view('scenario')
        ->with('Scenario', $Scenario);
    }
    public function talktest(Request $request)
    {
        $Bots = Models\Bot::All();
        $Scenarios = Models\Scenario::All();

        return view('talk')
        ->with('Bots', $Bots)
        ->with('Scenarios', $Scenarios);
    }

    public function addcell(Request $request, $id){
        $prevcell_id = $request->input('prevcell_id');
        $system = $request->input('system');
        $text = $request->input('text');

        $Scenario = Models\Scenario::find($id);
        $PrevScenarioCell = Models\ScenarioCell::find($prevcell_id);

        $NextScenarioCell = Models\ScenarioCell::create([
            'scenario_id' => $Scenario->id,
            'system' => $system
        ]);
        $PrevScenarioCell->NextCells()->save($NextScenarioCell);

        $CellSpeech = Models\CellSpeech::create([
            'scenario_cell_id' => $NextScenarioCell->id,
            'text' => $text
        ]);

        return redirect()->route('scenario', $id);
    }
}
