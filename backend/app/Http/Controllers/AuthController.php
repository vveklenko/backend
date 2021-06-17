<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\Email;


class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'login' => 'required|string|unique:users,login',
            'password' => 'required|string|confirmed',
            'email' => 'required|string|unique:users,email',
            'name' => 'string',
        ]);

        $user = User::create([
            'login' => $fields['login'],
            'password' => bcrypt($fields['password']),
            'email' => $fields['email'],
            'name' => $fields['name'],
        ]);

        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        $user->update(['remember_token' => $token]);

        return response($response, 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'login' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        //Check login
        $user = User::where('login', $fields['login'])->first();

        if(!$user) {
            return response([
                "message" => "This user does not exist!"
            ], 401);
        }

        if($fields['email'] != $user->email) {
            return response([
                "message" => "This email is incorrect!"
            ], 401);
        }

        //Check password
        if(!Hash::check($fields['password'], $user->password)) {
            return response([
                "message" => "Invalid password"
            ], 401);
        }

        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        $user->update(['remember_token' => $token]);

        return response($response, 201);
    }

    public function logout() {
        auth()->user()->update(['remember_token' => NULL]);

        return [
            'message' => 'Logged out'
        ];
    }

    public function send_email(Request $request) {
        $fileds = $request->validate([
            'email' => 'required|string',
        ]);

        $user = User::where('email', $fileds['email'])->first();

        if (!$user) {
            return [
                'message' => 'This email does not exist in database!'
            ];
        }

        $token = $user->createToken('mytoken')->plainTextToken;
        $user->update(['remember_token' => $token]);

        $details = [
            'title' => 'Link for reset password',
            'body' => URL::current().'/'.$token
        ];

        Mail::to($user)->send(new Email($details));

        return [
            'message' => 'Link was sent succeessfully!'
        ];
    }

    public function reset_password(Request $request, $token) {
        $fields = $request->validate([
            'password' => 'required|string|confirmed'
        ]);

        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return [
                'message' => 'Icorrect token!'
            ];
        }

        $user->update(['password' => bcrypt($fields['password'])]);
        $user->update(['remember_token' => NULL]);

        return [
            'message' => 'Password was changed!'
        ];
    }
}
