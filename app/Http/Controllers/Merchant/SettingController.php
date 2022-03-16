<?php
namespace App\Http\Controllers\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MerchantKey;

class SettingController extends Controller
{
    /**
     * Instantiate a new ClassController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:merchant');
    }

    public function apikey()
    {

        try{
            $userid = auth()->guard('merchant')->user()->id;
            $merchant_id = getMerchantId($userid);

            $data = MerchantKey::select('api_title','api_key','created_at')->where('merchnat_id',$merchant_id)->get();

            return response()->json(['data' => $data,'message' => 'Successfully fetch the data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'No record found!'], 409);
        }
    }

}
