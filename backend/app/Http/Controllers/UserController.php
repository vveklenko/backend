<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        foreach($users as $some) {
            $this->count_rate_user($some->id);
        }
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'login' => 'required|string|unique:users,login',
            'password' => 'required|string|confirmed',
            'email' => 'required|string|unique:users,email',
            'role' => 'required|string'
        ]);
            
        if(auth()->user()->role == 'admin')
            return User::create([
                'login' => $fields['login'],
                'password' => bcrypt($fields['password']),
                'email' => $fields['email'],
                'role' => $fields['role']
            ]);
        else 
            return response([
                "message" => "You cannot create a user because you are not admin"
            ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->count_rate_user($id);

        $user = User::find($id);
        if(!$user) 
            return response([
                "message" => "This user was not found"
            ], 404);
            
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user) 
            return response([
                "message" => "This user was not found"
            ], 404);
            
        if($user->id == auth()->user()->id || auth()->user()->role == 'admin')
            $user->update($request->all());
        else 
            return response([
                "message" => "You cannot change data"
            ], 401);
    
        // if(isset($request['picture']))
        //     return $this->avatar($request);

        return $user;
   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user) 
            return response([
                "message" => "This user was not found"
            ], 404);
            
        if($user->id == auth()->user()->id || auth()->user()->role == 'admin') {
            User::destroy($id);
            return [
                'message' => 'User was deleted'
            ];
        }
        else 
            return response([
                "message" => "You cannot delete this user!"
            ], 401);
    }

    public function avatar(Request $request) {
        $user = User::find(auth()->user()->id);
        $picture = public_path('avatars/').$user->id.'.'.$request->picture->extension();
        $request->picture->move(public_path('avatars'), $picture);
        $user->update(['picture' => $picture]);
        return response()->download($picture);
    }
}
