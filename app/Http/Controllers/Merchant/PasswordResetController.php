<?php
namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Merchant\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Models\UserMerchant;
use App\Models\Merchant;
use App\Models\PasswordReset;
use DB;
class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email',
        ]);

        $user = UserMerchant::where(['email' => $request->s_email,'status'=>'Active'])->first();

        if (!$user)
            return response()->json([
                'message' => "We can't find a user with that e-mail address."
            ], 404);
        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $request->s_email,
            'token' => Str::random(60)
        ]);
        //Get the token just created above
        $tokenData = DB::table('password_resets')
            ->where('email', $request->s_email)->first();

        if ($user && $tokenData) {
            $user->notify(new PasswordResetRequest($user,$tokenData->token));
            return response()->json([
                'message' => 'We have e-mailed your password reset link!'
            ]);
        } else {
            return response()->json([
                'error' => 'A Network Error occurred. Please try again.'
            ]);
            return redirect()->back()->withErrors(['error' => 'A Network Error occurred. Please try again.']);
        }
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }
        return response()->json($passwordReset);
    }
     /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
            'password_confirmation' => 'required|string',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        $user = UserMerchant::where('email', $passwordReset->email)->first();
        if (!$user)
            return response()->json([
                'message' => "We can't find a user with that e-mail address."
            ], 404);
        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        return response()->json([
            'message' => "Password Updated Successfully!",
            'user' =>$user
        ], 200);
    }
}
