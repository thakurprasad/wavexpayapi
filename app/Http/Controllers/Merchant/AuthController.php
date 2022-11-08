<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserMerchant;
use App\Models\Merchant;
use App\Models\MerchantKey;
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
            'merchant_salt' => 'required|string',
        ]);
        $remember_me = $request->has('remember') ? true : false;
		try{
			$expires_at = Carbon::now()->addWeeks(1);
			$ttl = 10080;

            $merchant_salt = $request->merchant_salt;
            $merchant_info = Merchant::select('id','merchant_name','contact_name','contact_phone')->where('access_salt',$merchant_salt)->where('status','Active')->first();
            
            if($merchant_info){
                $credentials = request(['email', 'password']);

                $credentials = array_merge($credentials, ['merchant_id'=>$merchant_info->id]);
                // Check Magic Password


                if (!$token = auth()->guard('merchant')->setTTL($ttl)->attempt($credentials)) {
                    return response()->json(['status' => 'error','message' => trans('auth.failed')], 401);
                }

                $merchant = auth()->guard('merchant')->user();
                
                if($request->mode=='test'){
                    $api_keys = MerchantKey::select('api_title','test_api_key','test_api_secret','created_at')->where('merchnat_id',$merchant_info->id)->get();
                }else{
                    $api_keys = MerchantKey::select('api_title','live_api_key','live_api_secret','created_at')->where('merchnat_id',$merchant_info->id)->get();
                }
                

                return response()->json([
                            'access_token' => $token,
                            'token_type' => 'Bearer',
                            'expires_at' => Carbon::parse(
                                $expires_at
                            )->toDateTimeString(),
                            'merchant'=>$merchant,
                            'merchant_info'=>$merchant_info,
                            'api_keys'=>$api_keys,
                            'message' => trans('auth.success'),
                            'status' => 'success',
                        ]);
            }else{
                return response()->json(['status' => 'error','message' => 'Authntication failed'], 409);
            }


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
            Auth::guard('merchant')->logout();

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
        $token = auth()->guard('merchant')->setTTL($ttl)->refresh();
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
