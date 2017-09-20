<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'signup_with' => 'required|string',
            'device_id' => 'required|integer',
            'gender' => 'required|string|in:'.implode(',', User::GENDER),
            // 'dob' => 'required|date_format:Y-m-d',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'signup_with' => $data['signup_with'],
            'device_id' => $data['device_id'],
            'name' => $data['name'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'phone' => $data['phone'],
            'profile_pic' => $data['profile_pic'],
            'country_id' => $data['country_id'],
        ]);

        return $this->returnJsonResponse(['message' => 'User created']);
        die('done');
    }
}
