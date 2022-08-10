<?php
namespace App\Http\Controllers\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\PaymentPage;

class PaymentPageController extends Controller
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
            $orderBy = ($request->get('orderby'))?$request->get('orderby'):'page_title';
            $offset = ($request->get('offset'))?$request->get('offset'):'15';

            $data = PaymentPage::where(function($query) use ($request) {
                if ($request->get('page_title'))
                    $query->where('page_title', 'like', $request->get('page_title')."%");
                if ($request->get('status'))
                    $query->where('status', $request->get('status'));
            })
            ->where('merchant_id',$merchantid
            )
            ->orderBy($orderBy,$sort)->paginate($offset);
            return response()->json(['data' => $data,'count'=> count($data),'message' => 'Successfully fetch data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'No record found!'], 409);
        }
    }

    public function store(Request $request)
    {
        $merchantid = auth()->guard('merchant')->user()->id;
        $input = $request->all();
        $this->validate($request, [
            'template_id' => 'required|numeric',
            'page_title' => 'required|string|min:3|max:50',
        ]);

        try{
            $data = PaymentPage::create($input);
            $created_id = $data->id;
            return response()->json(['created_id'=>$created_id,'message' => 'Data created successfully!'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => $e.'Failed to create Data!'], 409);
        }
    }

    public function show($id)
    {
        try{
            $data = PaymentPage::findOrFail($id);

            return response()->json(['data' => $data,'message' => 'Successfully fetch the data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'No record found!'], 409);
        }
    }

    public function update(Request $request, int  $id, PaymentPage $data)
    {
        $input = $request->all();
        $this->validate($request, [
            'template_id' => 'required|numeric',
            'page_title' => 'required|string|min:3|max:50',
        ]);

        try{
            $data       = PaymentPage::find($id);
            $data->template_id      = $input['template_id'];
            $data->page_title       = $input['page_title'];
            $data->page_content     = $input['page_content'];
            $data->status           = $input['status'];
            $data->fb_link          = $input['fb_link'];
            $data->twitter_link     = $input['twitter_link'];

            $data->whatsapp         = $input['whatsapp'];
            $data->support_email    = $input['support_email'];
            $data->support_phone    = $input['support_phone'];
            $data->term_conditions  = $input['term_conditions'];
            $data->payment_form_json = $input['payment_form_json'];
            $data->custom_url       = $input['custom_url'];
            $data->theme            = $input['theme'];
            $data->is_page_expiry   = $input['is_page_expiry'];
            if($input['is_page_expiry']==1){
                $data->expiry_date      = $input['expiry_date'];
            }else{
                $data->expiry_date      = null;
            }
            $data->successful_custom_message   = $input['successful_custom_message'];
            $data->successful_redirect_url   = $input['successful_redirect_url'];
            $data->facebook_pixel   = $input['facebook_pixel'];
            $data->google_analytics   = $input['google_analytics'];
            $data->save();

            return response()->json(['data'=>$data,'message' => 'Successfully updated the data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => $e.'Data Update Failed!'], 409);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = PaymentPage::findOrFail($id);
            $data->delete();

            return response()->json(['message' => 'Data deleted successfully!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Data deletion Failed!'], 409);
        }
    }

}
