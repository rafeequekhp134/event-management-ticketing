<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Validator,Redirect,Response,File,Storage;
use Illuminate\Support\Facades\Auth;
use App\Booking;
class ReportController extends Controller
{

    /**
     * Create a new ReportController instance.
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
                $report = Booking::where('bookings.user_id', Auth::guard()->user()->id)
                    ->join('events', 'bookings.event_id', '=', 'events.id')
                    ->join('capacities', 'bookings.type', '=', 'capacities.id')
                    ->select('events.name as event', 'bookings.*', 'capacities.type as capacity_type')
                    ->get();
                return response()->json(['success' => true, 'data' => $report], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Something went wrong' , 'message' => $e->getMessage()], 500);
        }
    }
}