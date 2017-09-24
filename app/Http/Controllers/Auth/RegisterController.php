<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Support\Facades\File;
use JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\ApiController;

class RegisterController extends ApiController
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
            'dob' => 'required|date_format:Y-m-d',
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
            $user = new User();
            $data['profile_pic'] = $this->uploadProfilePic($data);
            $user->fill($data);
            $user->active = 1;
            $user->save();

            return $user;
    }

    protected function uploadProfilePic($data)
    {
        $base64String = $data['profile_pic'];
        $file_name = User::PROFILE_PATH.sha1($data['email']).time().'.png';
        @list(, $base64String) = explode(';', $base64String);
        @list(, $base64String) = explode(',', $base64String);
        if($base64String!=""){
            $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.User::PROFILE_PATH);
            if (! file_exists($path)) {
                File::makeDirectory($path, 0777, true);
            }
            \Storage::disk('public')->put($file_name,base64_decode($base64String));
        }

        return $file_name;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required',
        'signup_with' => 'required',
        'device_id' => 'required',
        'gender' => 'required',
        'dob' => 'required',
        ]);

        if ($validator->fails()) {
            $failedRules = $validator->failed();
            if (isset($failedRules['email']['Unique'])) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Already register user.'
                ], 422);
            }
            if (isset($failedRules['email']['Email'])) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Failed to register, invalid email id.'
                ], 422);
            }
            return response()->json([
                'status' => 'fail',
                'message' => 'Failed to register, invalid parameters passed.'
            ], 422);
        }
        if (! $request->has(['name','email','password','signup_with','device_id','gender','dob'])) {
            return $this->abortJsonResponse([
                'status' => 'fail',
                'message' => 'Failed, mandatory parameters missing'
            ], 422);
        }

        event(new Registered($user = $this->create($request->all())));

        if (! $user) {
            $this->abortJsonResponse([
                'status' => 'fail',
                'message' => 'Something going wrong, please try again.'
            ], 422);
        }

        try {
            $token = JWTAuth::fromUser($user);
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully register user.',
                'data' => [
                    'token' => $token,
                ],
            ]);
        } catch (JWTAuthException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Please try again later.',
            ]);
        }
    }
}
