<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function showUsers(){
        // $users = DB::table('users')->where('id',2)->get();

        $users = DB::table('users')->whereNull('deleted_at')->paginate(3);
        return view('allUsers', ['data' => $users]);
        // return $users;
    }

    public function singleUser(string $id){
        $user = DB::table('users')->where('id', $id)->whereNull('deleted_at')->get();
        return view('user', ['data' => $user]);
    }

    public function addUser(Request $req){
        $user = DB::table('users')->insert([
            'name' => $req->username,
            'email' => $req->useremail,
            'age' => $req->userage,
            'city' => $req->usercity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if($user){
            return redirect()->route('home');
        }else{
            return "Error";
        }
    }

    public function updateUser(Request $req, $id){
        $user = DB::table('users')
        ->where('id', $id)
        ->update([
            'name' => $req->username,
            'email' => $req->useremail,
            'age' => $req->userage,
            'city' => $req->usercity,
            // 'created_at' => now(),
            'updated_at' => now(),
        ]);

        if($user){
            return redirect()->route('home');
        }else{
            return "Error";
        }
    }

    public function updatePage(string $id){
        $user = DB::table('users')->where('id', $id)->whereNull('deleted_at')->first();
        // return $user;
        return view('updateUser', ['data' => $user]);
    }

    public function deleteUser(string $id) {   //truncate resets the table
        $user = User::query()->findOrFail($id)->delete();
        if($user){
            return redirect()->route('home');
        }
    }
}
