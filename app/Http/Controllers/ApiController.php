<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use JWTAuth;
use JWTAuthException;
use App\User;

class ApiController extends Controller
{
	public function __construct()
	{
		$this->middleware('jwt.auth', ['only' => ['getAuthUser']]);
		$this->user = new User;
	}

    public function returnJsonResponse($data, $statusCode = 200) 
    {
    	return new JsonResponse($data, $statusCode);
    }

    public function abortJsonResponse($data, $statusCode)
    {
        $this->returnJsonResponse($data, $statusCode);
    }

    public function getAuthUser()
    {
    	$user = JWTAuth::toUser($request->token);        
        return response()->json(['result' => $user]);
    }
}
