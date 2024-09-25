<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyRequest;
use App\Model\Clas;
use App\Model\Lecture;
use App\Model\Student;
use App\Model\Task;
use App\Services\MyServicer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ApiTasksController extends Controller
{

    public function login(Request $request) {
        $credentials = $request->only('login', 'password');
        $credentials = array('username' => $credentials['login'], 'password' =>  $credentials['password'],);
        $login = 0;
        if ($credentials['username'] == 'Admin' && $credentials['password'] == '123') {
            $login = 1;
        }
        return Response::json( ['login' => $login] );
    }

    public function tskst(Request $request) {
        $id = $request->get('id');
        $task = Task::where('id', $id)->first();
        $task->is_complete = !$task->is_complete;
        $task->save();
        return Response::json( ['msg' => 'ok'] );
    }

    public function tskupd(Request $request) {
        $id = $request->get('id');
        $task = Task::where('id', $id)->first();
        $task->description = $request->get('val');
        $task->save();
        return Response::json( ['msg' => 'ok'] );
    }

}