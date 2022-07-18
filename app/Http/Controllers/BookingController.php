<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Validator,Redirect,Response,File,Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Booking;
class BookingController extends Controller
{

    /**
     * Create a new BookingController instance.
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
                
                $bookings = DB::table('bookings')
                            ->where('bookings.user_id', Auth::guard()->user()->id)
                            ->join('events', 'bookings.event_id', '=', 'events.id')
                            ->join('capacities', 'bookings.type', '=', 'capacities.id')
                            ->select('events.name as event', 'bookings.*', 'capacities.type as capacity_type')
                            ->get();

                return response()->json(['success' => true, 'data' => $bookings], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Something went wrong' , 'message' => $e->getMessage()], 500);
        }
    }
 
    public function save(Request $request)
    {
             
        try {
 
            $success = false;
            $data = null;
            
            $newBooking = $request->post();
            $booking = new Booking();
            $booking->customer_name = $newBooking['customer_name'];
            $booking->event_id = $newBooking['event_id'];
            $booking->type = $newBooking['type'];
            $booking->count = intval($newBooking['count']);
            $booking->amount = intval($newBooking['amount']);
            $booking->status = $newBooking['status'];
            $booking->user_id = Auth::guard()->user()->id;
            $success = $booking->save();
            $data = $booking;


            if ($success) {
                return response()->json([
                    "success" => true,
                    "message" => "Booking created successfully",
                    "data" => $data
                ]);
            } else {
                return response()->json(['error'=> 'Failed to create the booking'], 500);  
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
                
                $eventBookings = Booking::where([
                    'event_id' => $request->event_id,
                    'status' => 'Active'
                ])->get();

                return response()->json(['success' => true, 'data' => $eventBookings], 200);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
        } catch (\Exception $e) {
            
            return response()->json(['error' => 'Something went wrong' , 'message' => $e->getMessage()], 500);
        }
  
    }

    public function delete(Request $request)
    {
             
        try {
 
            $booking = Booking::find($request->id);

            if ($booking) {
                if ($booking->delete()) {
                    return response()->json([
                        "success" => true,
                        "message" => "Booking cancelled successfully"
                    ]);
                } else {
                    return response()->json(['error'=> 'Failed to cancel the booking'], 500);
                }
            } else {
                return response()->json(['error'=> 'The booking was not found'], 404);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong' , 'message' => $e->getMessage()], 500);
        }
  
    }
}