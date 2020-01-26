<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models as Models;

class ScenarioController extends Controller
{
  public function __construct()
  {
    // $this->middleware('auth');
  }

  public function new(Request $request)
  {
    $Scenario = new Models\Scenario;
    $AScenarios = Models\Scenario::all();

    return view('scenario.form')
    ->with('Scenario', $Scenario)
    ->with('AScenarios', $AScenarios)
    ->with('CellChains', []);
  }

  public function save(Request $request)
  {
    $Project = Models\Project::find(1);
    $Bot = Models\Bot::find(1);
    $AScenarios = Models\Scenario::all();

    try {
      DB::beginTransaction();

      $Scenario = new Models\Scenario;
      $Scenario->name = '';
      $Scenario->svg = '';
      $Scenario->key = bin2hex(random_bytes(10));
      $Bot->Scenarios()->save($Scenario);

      $params = $request->all();
      $this->set_cell($Bot, $Scenario, $params);

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

      return view('scenario.form')
      ->with('Scenario', $Scenario)
      ->with('AScenarios', $AScenarios)
      ->with('CellChains', $CellChains);
    }
  }

  public function edit(Request $request, $id)
  {
    $Scenario = Models\Scenario::find($id);
    $AScenarios = Models\Scenario::where('id', '!=', $id)->get();

    $CellChains = array();
    foreach ($Scenario->Cells as $Cell) {
      foreach ($Cell->NextChains as $NextChain) {
        $CellChains[$NextChain->svg_index] = $NextChain;
      }
    }

    return view('scenario.form')
    ->with('Scenario', $Scenario)
    ->with('AScenarios', $AScenarios)
    ->with('CellChains', $CellChains);
  }

  public function update(Request $request, $id)
  {
    $Project = Models\Project::find(1);
    $Bot = Models\Bot::find(1);
    $AScenarios = Models\Scenario::where('id', '!=', $id)->get();

    try {
      DB::beginTransaction();

      $Scenario = Models\Scenario::find($id);
      $Scenario->Cells()->delete();

      $params = $request->all();
      $this->set_cell($Bot, $Scenario, $params);

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

      return view('scenario.form')
      ->with('Scenario', $Scenario)
      ->with('AScenarios', $AScenarios)
      ->with('CellChains', $CellChains);
    }
  }

  private function set_cell($Bot, $Scenario, $params){
    $Scenario->name = $params['name'];
    $Scenario->svg = $params['svg'];
    $Scenario->save();

    $cells = [];
    if (isset($params['cells'])) {
      foreach ($params['cells'] as $key => $value) {
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

    if (isset($params['chains'])) {
      foreach ($params['chains'] as $key => $value) {
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
}
