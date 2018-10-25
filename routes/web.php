<?php

use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () {
    return "Please refer to the site admin or documentation on how to best use this API";
});

// STUDENTS ROUTES GROUP START HERE
$router->group(['prefix' => 'students'], function($router) {

    $router->get('/', function () {
        return DB::table('students')->get();
    });

    $router->post('/login', function (Request $req)
    {
        if ($req->filled(['password', 'email'])) {
            $data = DB::table('students')
            ->where('email',  $req->email)
            ->get();
            if(isset($data[0]->password)){
                return (password_verify($req->password, $data[0]->password))?  $data :'Authentication failed';
            }
        }
        return 'Authentication failed';

    });

    $router->post('/', function (Request $req) {
        if ($req->filled('matricno')) {
            return DB::table('students')->where('matricno', $req->matricno)->get();
        }
        return 'Request failed';
    });

    $router->post('/add', function (Request $req) {
        $data = $req->all();
        $data['password'] = password_hash($req->password, PASSWORD_DEFAULT);
        $val = DB::table('students')->insert([
            $data
        ]);
        return ($val)?'Added successfully':'Request failed';
    });

    $router->post('/delete', function () {
        if ($req->filled('matricno')) {
            return (DB::table('students')->where('matricno', $req->matricno)->delete()) ? "Deleted successfully" : "Request failed";;
        }
        return 'Request failed';
    });

    $router->post('/update', function (Request $req) {
        if ($req->filled('matricno')) {
            return (DB::table('students')
            ->where('matricno', $req->matricno)
            ->update(
                ['updated_at' => DB::raw('NOW()')],
                $req->all()
                ))?"Updated successfully":"Request failed";
            }
            return 'Request failed';
        });

    });

// STAFFS ROUTES GROUP START HERE
$router->group(['prefix' => 'staffs'], function($router) {

    $router->post('/add', function (Request $req) {
        $data = $req->all();
        $data['password'] = password_hash($req->password, PASSWORD_DEFAULT);
        $val = DB::table('staffs')->insert([
            $data
        ]);
        return ($val)?'Added successfully':'Request failed';
    });

    $router->get('/', function () {
        return DB::table('staffs')->get();
    });

    $router->post('/login', function (Request $req) {
        if ($req->filled(['password', 'email'])) {
            $data = DB::table('staffs')
            ->where('email',  $req->email)
            ->get();
            if(isset($data[0]->password)){
                return (password_verify($req->password, $data[0]->password)) ? $data : 'Authentication failed';
            }
        }
        return 'Authentication failed';
    });

    $router->post('/', function (Request $req) {
        return DB::table('staffs')->where('email', $req->email)->get();
    });

    $router->post('/delete', function (Request $req) {
        return (DB::table('staffs')->where('id', $req->id)->delete()) ? "Deleted successfully" : "Request failed";;
    });

    $router->post('/update', function (Request $req) {
        if ($req->filled('id')) {
            return (DB::table('staffs')
            ->where('email', $req->email)
            ->update(
                ['updated_at' => DB::raw('NOW()')],
                $req->all()
                )) ? "Updated successfully" : "Request failed";
        }
        return 'Request failed';
    });

});

// PROJECTS ROUTES GROUP START HERE
$router->group(['prefix' => 'projects'], function($router) {

    $router->post('/add', function (Request $req) {
        $val = DB::table('projects')->insert([
            $req->all()
        ]);
        return ($val)?'Added successfully':'Error occurred, try again';
    });

    $router->post('/delete', function (Request $req) {
        if ($req->filled('id')) {
            return (DB::table('projects')->where('id', $req->id)->delete()) ? "Deleted successfully" : "Request failed";
        }
        return 'Request failed';
    });

    $router->post('/pending', function (Request $req) {
        if ($req->filled('email')) {
            return DB::table('projects')
            ->join('students','projects.student','=', 'students.matricno')
            ->select('projects.*', 'students.*')
            ->distinct()
            ->where([
                ['projects.supervisor', '=', $req->email],
                ['projects.status', '=', 0]
            ])
            ->get();
        }
        return 'Request failed';
    });

    $router->post('/approved', function (Request $req) {
        if ($req->filled('email')) {
            return DB::table('projects')
            ->join('students','projects.student','=', 'students.matricno')
            ->select('projects.*', 'students.*')
            ->distinct()
            ->where([
                ['projects.supervisor', '=', $req->email],
                ['projects.status', '=', 1]
            ])
            ->get();
        }
        return 'Request failed';
    });

    $router->post('/update', function (Request $req) {
        return (DB::table('projects')
        ->where('id', $req->id)
        ->update(
            ['updated_at' => DB::raw('NOW()')],
            $req->all()
            ) ) ? "Updated successfully" : "Request failed";
        });

        $router->get('/', function () {
            return DB::table('projects')->get();
        });

        $router->post('/', function (Request $req) {
            if ($req->filled('id')) {
                return DB::table('projects')->where('id', $req->id)->get();
            }
            return 'Request failed';
        });
    });
