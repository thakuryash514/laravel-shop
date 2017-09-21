<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function returnJsonResponse($data, $statusCode = 200) 
    {
    	return new JsonResponse($data, $statusCode);
    }

    public function abortJsonResponse($data, $statusCode)
    {
        $this->returnJsonResponse($data, $statusCode);
    }
}
