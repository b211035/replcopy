<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models as Models;

class DictionaryController extends Controller
{
  public function __construct()
  {
    // $this->middleware('auth');
  }

  public function index(Request $request)
  {
    $Projects = Models\Project::all();

    $Bot = Models\Bot::find(1);

    return view('dictionary.index')
    ->with('Projects', $Projects)
    ->with('Bot', $Bot);
  }

  public function new(Request $request)
  {
    $Projects = Models\Project::all();

    $Dictionary = new Models\Dictionary;

    return view('dictionary.form')
    ->with('Projects', $Projects)
    ->with('Dictionary', $Dictionary);
  }

  public function save(Request $request)
  {
    $Projects = Models\Project::all();

    $Bot = Models\Bot::find(1);

    $params = $request->all();

    try {
      DB::beginTransaction();

      $Dictionary = new Models\Dictionary;
      $Dictionary->name = $params['name'];
      $Bot->Dictionaries()->save($Dictionary);

      $params['words'] = preg_replace('/\r\n/', '\n', $params['words']);
      $params['words'] = preg_replace('/\r/', '\n', $params['words']);
      $words = explode('\n', $params['words']);

      foreach ($words as $word) {
        $Word = new Models\Word;
        $Word->name = $word;
        $Word->another_name = $word;
        $Dictionary->Words()->save($Word);
      }

      DB::commit();
      return redirect()->route('dictionary');
    } catch (Exception $e) {
      DB::rollBack();
      $CellChains = array();
      foreach ($Dictionary->Cells as $Cell) {
        foreach ($Cell->NextChains as $NextChain) {
          $CellChains[$NextChain->svg_index] = $NextChain;
        }
      }

      return view('dictionary.form')
      ->with('Projects', $Projects)
      ->with('Dictionary', $Dictionary);
    }
  }

  public function edit(Request $request, $id)
  {
    $Projects = Models\Project::all();

    $Dictionary = Models\Dictionary::find($id);

    return view('dictionary.form')
    ->with('Projects', $Projects)
    ->with('Dictionary', $Dictionary);
  }

  public function update(Request $request, $id)
  {
    $Projects = Models\Project::all();

    $Bot = Models\Bot::find(1);

    $params = $request->all();

    try {
      DB::beginTransaction();

      $Dictionary = Models\Dictionary::find($id);
      $Dictionary->Words()->delete();

      $Dictionary->name = $params['name'];
      $Bot->Dictionaries()->save($Dictionary);

      $params['words'] = preg_replace('/\r\n/', '\n', $params['words']);
      $params['words'] = preg_replace('/\r/', '\n', $params['words']);
      $words = explode('\n', $params['words']);

      foreach ($words as $word) {
        $Word = new Models\Word;
        $Word->name = $word;
        $Word->another_name = $word;
        $Dictionary->Words()->save($Word);
      }

      DB::commit();
      return redirect()->route('dictionary');
    } catch (Exception $e) {
      DB::rollBack();
      $CellChains = array();
      foreach ($Dictionary->Cells as $Cell) {
        foreach ($Cell->NextChains as $NextChain) {
          $CellChains[$NextChain->svg_index] = $NextChain;
        }
      }

      return view('dictionary.form')
      ->with('Projects', $Projects)
      ->with('Dictionary', $Dictionary);
    }
  }
}
