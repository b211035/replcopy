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

  public function variable(Request $request)
  {
    return view('welcome');
  }

  public function talktest(Request $request)
  {
    $Projects = Models\Project::all();

    $Bots = Models\Bot::All();
    $Scenarios = Models\Scenario::All();

    return view('talk')
    ->with('Projects', $Projects)
    ->with('Bots', $Bots)
    ->with('Scenarios', $Scenarios);
  }

  // APIKEY
  // bin2hex(random_bytes(20))

  // ボットID
  // bin2hex(random_bytes(10))
}
