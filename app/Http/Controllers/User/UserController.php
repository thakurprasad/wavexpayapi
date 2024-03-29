<?php

namespace App\Http\Controllers\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\UpdatePassword;
use App\Models\Batch;
use Illuminate\Support\Facades\Date;

class UserController extends Controller
{
    /**
     * Instantiate a new ClassController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile_update(Request $request)
    {
        $messages = [
            'name.required' => 'Name is required',
        ];
        //validate incoming request
        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        try{
            $userid = auth()->guard('user')->user()->id;
            $data= User::find($userid);
            $updateArr = [
                "name" => $request->post('name'),
            ];
            $old_password = ($request->post('old_password'));
            $get_password = ($request->post('new_password'));
            if($old_password && $get_password){
                if( strlen($get_password) < 6 ){
                    return response()->json(['message' => 'New password must be minimum 6 characters long.'], 422);
                }

                if( !Hash::check($old_password, $data->password) ){
                    return response()->json(['message' => 'Old password is incorrect.'], 422);
                }
            } elseif ($old_password && !$get_password) {
                return response()->json(['message' => 'New password is required.'], 422);
            } elseif ( !$old_password && $get_password) {
                return response()->json(['message' => 'Old password is required.'], 422);
            }

            if($get_password && $old_password){
                $updateArr['password'] = Hash::make($get_password);
            }
            $updateDb =  User::where('id', $userid)->update($updateArr);
            return response()->json([ 'message' => 'Profile updated successfully.'], 200);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Profile update Failed!'], 409);
        }
    }

    public function getProfile()
    {
        try{
            $userid = auth()->guard('user')->user()->id;
            $data= User::find($userid);

            return response()->json(['data' => $data ], 200);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Failed to fetch Data!'], 409);
        }
    }
}
