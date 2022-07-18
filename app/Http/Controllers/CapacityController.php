<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Validator,Redirect,Response,File,Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Capacity;

class CapacityController extends Controller
{

    /**
     * Create a new CapacityController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function index () {
        try {

            if (Auth::guard()->user()) {
                
                $capacities = Capacity::get();

                return response()->json(['success' => true, 'data' => $capacities], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Something went wrong' , 'message' => $e->getMessage()], 500);
        }
    }
 
    public function getByEvent(Request $request)
    {
             
        try {

            if (!$request->event_id) {
                return response()->json(['error' => 'Event id not found'], 400);
            }

            if (Auth::guard()->user()) {
                
                $eventCapacities = Capacity::where('event_id', $request->event_id)->get();

                return response()->json(['success' => true, 'data' => $eventCapacities], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Something went wrong' , 'message' => $e->getMessage()], 500);
        }
  
    }
}