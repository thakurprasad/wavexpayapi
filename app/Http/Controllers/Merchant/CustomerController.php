<?php
namespace App\Http\Controllers\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Customer;

class UserMerchantController extends Controller
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
            $orderBy = ($request->get('orderby'))?$request->get('orderby'):'customer_id';
            $offset = ($request->get('offset'))?$request->get('offset'):'15';

            $data = Customer::where(function($query) use ($request) {
                if ($request->get('customer_id'))
                    $query->where('customer_id', 'like', $request->get('customer_id')."%");
                if ($request->get('name'))
                    $query->where('name', 'like', $request->get('name')."%");
                if ($request->get('contact'))
                    $query->where('contact', 'like', "%".$request->get('contact')."%");
                if ($request->get('gstin'))
                    $query->where('gstin', 'like', "%".$request->get('gstin')."%");
                if ($request->get('notes'))
                    $query->where('notes', 'like', "%".$request->get('notes')."%");

            })
            ->where('merchant_id',$request->get('merchant_id'))
            ->orderBy($orderBy,$sort)->paginate($offset);
            return response()->json(['data' => $data,'count'=> count($data),'message' => 'Successfully fetch data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'No record found!'], 409);
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $this->validate($request, [
            'merchant_id' => 'required|numeric',
            'customer_id' => 'required|string|min:2|max:150',
            'name' => 'required|string|min:3|max:50',
            'contact' => 'required|string|min:13|max:15',
            'email' => 'required|email',
        ]);

        try{
            $data = Customer::create($input);
            $created_id = $data->id;
            return response()->json(['created_id'=>$created_id,'message' => 'Data created successfully!'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Failed to create Data!'], 409);
        }
    }

    public function show($id)
    {
        try{
            $data = Customer::findOrFail($id);

            return response()->json(['data' => $data,'message' => 'Successfully fetch the data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'No record found!'], 409);
        }
    }

    public function update(Request $request, int  $id, Customer $data)
    {
        $input = $request->all();
        $this->validate($request, [
            'merchant_id' => 'required|numeric',
            'customer_id' => 'required|string|min:2|max:150',
            'name' => 'required|string|min:3|max:50',
            'contact' => 'required|string|min:13|max:15',
            'email' => 'required|email',
        ]);

        try{
            $data       = Customer::find($id);
            $data->customer_id      = $input['customer_id'];
            $data->name             = $input['name'];
            $data->email            = $input['email'];
            $data->contact          = $input['contact'];
            $data->gstin            = $input['gstin'];
            $data->notes            = $input['notes'];
            $data->save();

            return response()->json(['data'=>$data,'message' => 'Successfully updated the data!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Data Update Failed!'], 409);
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
            $data = Customer::findOrFail($id);
            $data->delete();

            return response()->json(['message' => 'Data deleted successfully!' ], 200);
        }catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Data deletion Failed!'], 409);
        }
    }

}
