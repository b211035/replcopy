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

    $choiceProject = Models\Project::find(1);
    return view('dashbord')
    ->with('Projects', $Projects)
    ->with('choiceProject', $choiceProject);
  }
  public function dictionary(Request $request)
  {
    return view('welcome');
  }
  public function variable(Request $request)
  {
    return view('welcome');
  }
  public function new_scenario(Request $request)
  {
    $Scenario = new Models\Scenario;

    return view('scenario')
    ->with('Scenario', $Scenario)
    ->with('CellChains', []);
  }

  public function scenario(Request $request, $id)
  {
    $Scenario = Models\Scenario::find($id);

    $CellChains = array();
    foreach ($Scenario->Cells as $Cell) {
      foreach ($Cell->NextChains as $NextChain) {
        $CellChains[$NextChain->svg_index] = $NextChain;
      }
    }

    return view('scenario')
    ->with('Scenario', $Scenario)
    ->with('CellChains', $CellChains);
  }

  public function save_scenario(Request $request)
  {
    $Project = Models\Project::find(1);
    $Bot = Models\Bot::find(1);
    $Scenario = new Models\Scenario;
    $Scenario->name = '';
    $Scenario->svg = '';
    $Scenario->key = bin2hex(random_bytes(10));
    $Bot->Scenarios()->save($Scenario);

    $all = $request->all();
    $this->set_scenario($Bot, $Scenario, $all);

    return redirect()->route('dashbord');
  }

  public function update_scenario(Request $request, $id)
  {
    $Project = Models\Project::find(1);
    $Bot = Models\Bot::find(1);

    try {
      DB::beginTransaction();

      $Scenario = Models\Scenario::find($id);
      $Scenario->Cells()->delete();

      $all = $request->all();
      $this->set_scenario($Bot, $Scenario, $all);

      DB::commit();
      return redirect()->route('dashbord');
    } catch (Exception $e) {
      DB::rollBack();
      $CellChains = array();
      foreach ($Scenario->Cells as $Cell) {
        foreach ($Cell->NextChains as $NextChain) {
          $CellChains[$NextChain->svg_index] = $NextChain;
        }
      }

      return view('scenario')
      ->with('Scenario', $Scenario)
      ->with('CellChains', $CellChains);
    }
  }

  private function set_scenario($Bot, $Scenario, $all){
    $Scenario->name = $all['name'];
    $Scenario->svg = $all['svg'];
    $Scenario->save();

    $cells = [];
    if (isset($all['cells'])) {
      foreach ($all['cells'] as $key => $value) {
        $Cell = new Models\ScenarioCell;
        $Cell->system = $value['system'];
        $Cell->svg_index = $value['svg_index'];
        $Scenario->Cells()->save($Cell);  

        if (isset($value['condition'])) {
          foreach ($value['condition'] as $condition) {
            if (empty($condition['variable'])) {
              continue;
            }
            $CellCondition = new Models\CellCondition;
            $CellCondition->variable_id = $this->set_val($Bot, $condition['variable']);
            $CellCondition->condition = is_null($condition['condition']) ? 0 :$condition['condition'];
            $CellCondition->condition_value = $condition['condition_value'];

            $Cell->Conditions()->save($CellCondition);  
          }
        }

        if (isset($value['speech'])) {
          foreach ($value['speech'] as $speech) {
            if (empty($speech['text'])) {
              continue;
            }
            $CellSpeech = new Models\CellSpeech;
            $CellSpeech->text = is_null($speech['text']) ? 0 :$speech['text'];
            $CellSpeech->condition = is_null($speech['condition']) ? 0 :$speech['condition'];

            $Cell->Speeches()->save($CellSpeech);  
          }
        }

        if (isset($value['memory'])) {
          foreach ($value['memory'] as $memory) {
            if (empty($memory['variable'])) {
              continue;
            }
            $CellMemory = new Models\CellMemory;
            $CellMemory->variable_id = $this->set_val($Bot, $memory['variable']);
            $CellMemory->condition = is_null($memory['condition']) ? 0 :$memory['condition'];
            $CellMemory->condition_value = $memory['condition_value'];

            $Cell->Memorys()->save($CellMemory);  
          }
        }

        $cells[$Cell->svg_index] = $Cell;
      }
    }

    if (isset($all['chains'])) {
      foreach ($all['chains'] as $key => $value) {
        $CellChain = new Models\CellChain;

        $CellChain->svg_index = $value['svg_index'];
        $CellChain->prev_cell_id = $cells[$value['prev']]->id;
        $CellChain->next_cell_id = $cells[$value['next']]->id;

        $CellChain->save();  
      }
    }
    return $Scenario;
  }

  private function set_val($Bot, $name){
    $Variable = $Bot->Variables->where('name', $name)->first();
    if (empty($Variable)) {
      $Variable = new Models\Variable;
      $Variable->name = $name;
      $Bot->Variables()->save($Variable);
    }
    return $Variable->id;
  }

  public function talktest(Request $request)
  {
    $Bots = Models\Bot::All();
    $Scenarios = Models\Scenario::All();

    return view('talk')
    ->with('Bots', $Bots)
    ->with('Scenarios', $Scenarios);
  }

  // public function addcell(Request $request, $id){
  //     $prevcell_id = $request->input('prevcell_id');
  //     $system = $request->input('system');
  //     $text = $request->input('text');

  //     $Scenario = Models\Scenario::find($id);
  //     $PrevScenarioCell = Models\ScenarioCell::find($prevcell_id);

  //     $NextScenarioCell = Models\ScenarioCell::create([
  //         'scenario_id' => $Scenario->id,
  //         'system' => $system
  //     ]);
  //     $PrevScenarioCell->NextCells()->save($NextScenarioCell);

  //     $CellSpeech = Models\CellSpeech::create([
  //         'scenario_cell_id' => $NextScenarioCell->id,
  //         'text' => $text
  //     ]);

  //     return redirect()->route('scenario', $id);
  // }

  // APIKEY
  // bin2hex(random_bytes(20))

  // ボットID
  // bin2hex(random_bytes(10))
}
