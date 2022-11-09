<?php

namespace App\Http\Controllers\Merchant;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }


    /**
     * $result is response data array
     * $message is take message about response data  
     * $code is response code
     * */
    public function sendSuccess($result, $message, $code = 200)
    {
        $response = [
          //  'success' => true,
            'code'          => $code, 
            'status'        =>'success',
            'message'       => $message,
            'data'          => $result,
            
        ];
        return response()->json($response, $code);
    }


   
    /**
     * $error - in case display error statement -string type
     * $errorMessages - in case multiple error list in array formate
     * $code is response code
     * */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            //'success' => false,
            'code' => $code,
            'status' =>'failed',
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

}
