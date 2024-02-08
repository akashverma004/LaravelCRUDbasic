<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function showUsers(){
        // $users = DB::table('users')->where('id',2)->get();

        $users = DB::table('users')->paginate(3);
        return view('allUsers', ['data' => $users]);
        // return $users;
    }

    public function singleUser(string $id){
        $user = DB::table('users')->where('id', $id)->get();
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
        $user = DB::table('users')->find($id);
        // return $user;
        return view('updateUser', ['data' => $user]);
    }

    public function deleteUser(string $id) {   //truncate resets the table
        $user = DB::table('users')->where('id', $id)->delete();
        if($user){
            return redirect()->route('home');
        }
    }
}
