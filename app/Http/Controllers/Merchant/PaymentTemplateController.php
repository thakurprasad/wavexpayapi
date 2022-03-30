<?php
namespace App\Http\Controllers\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PaymentTemplate;

class PaymentTemplateController extends Controller
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

    public function index(Request $request)
    {
        $userid = auth()->guard('merchant')->user()->id;
        $merchantid = auth()->guard('merchant')->user()->id;
        try{
            $sort = ($request->get('sort')=='DESC')?'DESC':'ASC';
            $orderBy = ($request->get('orderby'))?$request->get('orderby'):'title';
            $payment_type = ($request->get('payment_type'))?$request->get('payment_type'):'Page';

            $data = PaymentTemplate::where('status','Active')->where('payment_type',$payment_type)
            ->orderBy($orderBy,$sort)->get();
            return response()->json(['data' => $data,'count'=> count($data),'message' => 'Successfully fetch data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'No record found!'], 409);
        }
    }

    public function show($id)
    {
        try{
            $data = PaymentTemplate::findOrFail($id);

            return response()->json(['data' => $data,'message' => 'Successfully fetch the data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'No record found!'], 409);
        }
    }

}
