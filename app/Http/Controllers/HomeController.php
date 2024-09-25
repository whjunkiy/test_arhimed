<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller as Controller;
use App\Model\Task;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct()
    {

//        $this->middleware('auth');
    }

    public function index()
    {
        $taks = Task::all()->sortByDesc('id');
        return view('internal.home.index', [
            'tasks' => $taks,
            'json' => $taks->toArray(),
        ]);
        //return redirect(config('app.url_legacy'));
    }

}
