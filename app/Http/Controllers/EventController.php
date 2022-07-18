<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Validator,Redirect,Response,File,Storage;
use Illuminate\Support\Facades\Auth;
use App\Event;
use App\Capacity;
class EventController extends Controller
{

    /**
     * Create a new EventController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function index () {
        try {

            $today = date('Y-m-d');
            if (Auth::guard()->user()) {
                $events = Event::where('user_id', Auth::guard()->user()->id)
                    ->where('start_date', '>', $today)
                    ->get();
                return response()->json(['success' => true, 'data' => $events], 200);
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
            
            $newEvent = $request->post();
            $event = new Event();
            $event->name = $newEvent['name'];
            $event->status = $newEvent['status'];
            $event->start_date = $newEvent['start_date'];
            $event->end_date = $newEvent['end_date'];
            $event->user_id = Auth::guard()->user()->id;
            $success = $event->save();
            $data = $event;


            if ($success) {

                /* Create the Capacity */
                if ($newEvent['capacity'] && !empty($newEvent['capacity'])) {

                    $newCapacity = $newEvent['capacity'];

                    foreach ($newCapacity as $type => $capacity) {

                        $createCapacity = new Capacity();
                        $createCapacity->event_id = $event->id;
                        $createCapacity->type = $type;
                        $createCapacity->capacity = intval($capacity['capacity']);
                        $createCapacity->price = intval($capacity['price']);
                        $createCapacity->user_id = Auth::guard()->user()->id;
                        $createCapacity->save();

                    }
                }
                
                return response()->json([
                    "success" => true,
                    "message" => "Event created successfully",
                    "data" => $data
                ]);
            } else {
                return response()->json(['error'=> 'Failed to create the event'], 500);  
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong' , 'message' => $e->getMessage()], 500);
        }
  
    }

    public function delete(Request $request)
    {
             
        try {
 
            $event = Event::find($request->id);

            if ($event) {
                if ($event->delete()) {
                    return response()->json([
                        "success" => true,
                        "message" => "Event deleted successfully"
                    ]);
                } else {
                    return response()->json(['error'=> 'Failed to delete the event'], 500);
                }
            } else {
                return response()->json(['error'=> 'The event was not found'], 404);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong' , 'message' => $e->getMessage()], 500);
        }
  
    }
}