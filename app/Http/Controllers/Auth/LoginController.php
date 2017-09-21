<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Failed, mandatory parameters missing.'], 422);
        }
        try {
            $user = User::query()->whereEmail($request->input('email'))
                ->wherePassword($request->input('password'))
                ->firstOrFail();
            $user->device_id = $request->input('device_id');
            $user->save();

            if (! $user->active) {
                return response()->json(['message' => 'Failed to login, your account is inactive.'], 422);
            }
            return response()->json(['message' => 'Successfully login.'], 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Failed to login, credentials are incorrect.'], 404);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'Please try again later.'], 422);
        }
    }
}
