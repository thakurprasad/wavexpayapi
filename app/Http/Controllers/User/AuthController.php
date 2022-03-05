<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

use Illuminate\Support\Str;
use DB;

use App\Notifications\PasswordResetMismatch;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;

class AuthController extends Controller
{

     /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {


		// Trim inputs and store
		$email = trim($request->input('email'));
		// Overwrite inputs
		request()->merge(['email'=>$email]);

		//validate incoming request
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $remember_me = $request->has('remember') ? true : false;
		try{
			$expires_at = Carbon::now()->addWeeks(1);
			$ttl = 10080;



			$credentials = request(['email', 'password']);

			// Check Magic Password


			if (!$token = auth()->guard('user')->setTTL($ttl)->attempt($credentials)) {
				return response()->json(['status' => 'error','message' => trans('auth.failed')], 401);
			}

			$user = auth()->guard('user')->user();
			return $this->respondWithToken($token);

		} catch (\Exception $e) {
			//dd($e);
            //return error message
            return response()->json(['status' => 'error','message' => $e], 409);
        }

    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout()
    {
        try{
            Auth::guard('user')->logout();

            return response()->json([
                'status' => 'success',
                'message' => trans('message.success_logout')
            ], 200);

        }catch (\Exception $e) {
            //return error message
            return response()->json(['status' => 'error','message' => trans('message.failed_logout')], 409);
        }

    }



    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $expires_at = Carbon::now()->addWeeks(2);
        $ttl = 20160;
        $token = auth()->guard('user')->setTTL($ttl)->refresh();
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $expires_at
            )->toDateTimeString(),
        ]);
    }


	protected function respondWithToken($token)
    {
		try{
            $expires_at = Carbon::now()->addWeeks(1);
            $ttl = 10080;

            return response()->json([
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse(
                            $expires_at
                        )->toDateTimeString(),
                        'message' => trans('auth.success'),
                        'status' => 'success',
                    ]);
		}
		catch(\Exception $e){
			return response()->json(['msg'=>$e->getMessage()]);
		}
    }


}
