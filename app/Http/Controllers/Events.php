<?php

namespace App\Http\Controllers;
use App\Models\Event;

use Illuminate\Http\Request;
use Validator;
use Mail;

class Events extends Controller
{

    public function __construct(){
        // only login user will be access this class;
       // $this->middleware('auth');
    }


     // For API, Get the list of events
    function list(){

        $list = Event::all();
        $msg = 'Something Wrong';
        if($list->isNotEmpty()){

            $data = $list->toArray();
            $msg = 'Data Successfully Fetched';

        }else{
            $msg = 'Data not found';
        }
        return response()->json([
                    'success' => 1,
                    'message' => $msg,
                    'data'    => $data
        ]);

    }

     // For API,  Get the list of active events
    function getActiveEvents(){

        $list = Event::where('created_at','>=', date('Y-m-d').' 00:00:00')->get();
       
        $data = null;
       $msg = 'Something Wrong';
        if($list->isNotEmpty()){

            $data = $list->toArray();
            $msg = 'Data Successfully Fetched';

        }else{
            $msg = 'Data not found';
        }
        return response()->json([
                    'success' => 1,
                    'message' => $msg,
                    'data'    => $data
         ]);

    }


   // For API,  Create and upate event information
    function create(Request $request){
        $rules=array(
             'name' => 'required',
            'slug' => 'required|unique:events,slug,'.$request->id,
        );
        $validator=Validator::make($request->all(),$rules);
        $success_response = 0;
        $data =  null;
        $msg = 'Something Wrong';
        if($validator->fails()){
           $msg = $validator->errors();
        }else{

            $record = array(
                    'name' => $this->inputStripTag($request->name),
                    'slug' => $this->inputStripTag($request->slug),
            );
            $dataHas = Event::where('id',$request->id)->get()->first();
            if($dataHas){
                $update_obj = $dataHas->update($record);
                $success_response = 1;
                $data = $update_obj;
                $msg = 'Updated Successfully';
            }else{
                $save_obj = Event::create($record);
            
                if($save_obj){
                    $success_response = 1;
                    $data = $save_obj;
                    $msg = 'Save Successfully';
                }
            }
        }

        return response()->json([
                    'success' => $success_response,
                    'message' => $msg,
                    'data'    => $data,
        ]);
    }

    // For API,  Get the event information by id
    function getEvent(Request $request){

        $list = Event::where('id',$request->id)->get()->first();
        $data = null;
        $msg = 'Something Wrong';
        if(isset($list)){

            $data = $list->toArray();
            $msg = 'Data Successfully Fetched';

        }else{
            $msg = 'Data not found';
        }
        return response()->json([
                    'success' => 1,
                    'message' => $msg,
                    'data'    => $data
         ]);
    }

    // For API,  Update event information by id
    function updateEvent(Request $request){
        
        $rules=array(
             'name' => 'required',
            'slug' => 'required|unique:events,slug,'.$request->id,
        );
        $validator=Validator::make($request->all(),$rules);
        $success_response = 0;
        $data =  null;
        $msg = 'Something Wrong';
        if($validator->fails()){
           $msg = $validator->errors();
        }else{

            $record = array(
                    'name' => $this->inputStripTag($request->name),
                    'slug' => $this->inputStripTag($request->slug),
            );
            $dataHas = Event::where('id',$request->id)->get()->first();
            if($dataHas){
                $update_obj = $dataHas->update($record);
                $success_response = 1;
                $data = $update_obj;
                $msg = 'Updated Successfully';
            }else{
                $save_obj = Event::create($record);
            
                if($save_obj){
                    $success_response = 1;
                    $data = $save_obj;
                    $msg = 'Save Successfully';
                }
            }
        }

        return response()->json([
                    'success' => $success_response,
                    'message' => $msg,
                    'data'    => $data,
        ]);
       
    }

    // Remove the html tags and some special charators for request input  
    function inputStripTag($data) {

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;

    }

    // For API,  delete event by id
    function delete(Request $request){

        $list = Event::where('id',$request->id)->get()->first();
        $data = null;
        $msg = 'Something Wrong';
        if(isset($list)){

            $data = $list->delete();
            $msg = 'Deleted Successfully';

        }else{
            $msg = 'Data not found';
        }
        return response()->json([
                    'success' => 1,
                    'message' => $msg,
                    'data'    => $data
         ]);
       
    }

    // For Browser,  Show the events list with search keyword
    function manageEventsList(Request $request){
        
        $search_text = '';
        if(isset($request->search_event)){
            $search_text = $request->search_event;
          $list = Event::where('name', 'LIKE', '%'.$search_text.'%')->orWhere('slug', 'LIKE', '%'.$search_text.'%')->paginate(10) ;
        }else{
            $list = Event::paginate(10) ;
        }

        
        return view('events_list',compact('list','search_text'));
    }

    // For Browser,  Show the add event form
    function addEventForm(Request $request){

        return view('event_add_edit');
    }

    // For Browser,  delete event function
    function deleteEvent(Request $request){

        $list = Event::where('id',$request->id)->get()->first();
        $data = null;
        $msg = 'Something Wrong';
        if(isset($list)){
            $data = $list->delete();
            $msg = 'Deleted Successfully';
            $type = 'success';
        }else{
            $msg = 'Data not found';
            $type = 'error';
            
        }
        return redirect()->back()->with('message_'.$type, $msg)->with('type', $type);
       
    }
 // For Browser,  add event function with validation
    function addEvent(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|unique:events,slug,'.$request->id,
        ]);

         $record = array(
                    'name' => $this->inputStripTag($request->name),
                    'slug' => $this->inputStripTag($request->slug),
        );
        $dataHas = Event::where('id',$request->id)->get()->first();
        $type = 'error';
        $msg = 'Something Wrong';
        if($dataHas){
            $update_obj = $dataHas->update($record);
            $success_response = 1;
            $data = $update_obj;
            $msg = 'Updated Successfully';
            $type = 'success';
        }else{
            $save_obj = Event::create($record);

        
            if($save_obj){
                $success_response = 1;
                $data = $save_obj;
                $msg = 'Save Successfully.';
                $type = 'success';
                $mail_details = array();
                $mail_details['to'] = 'rahulbari1839@gmail.com';
                $mail_details['from'] = 'rahulbari1839@gmail.com';
                $mail_details['subject'] = 'Event  created.';
                $mail_details['body'] = 'The event ('.$record['name'].') is created Successfully.';
                try{
                   
                    Mail::to($mail_details['to'])->send(new \App\Mail\NotifyCreateEvent($mail_details));
                    if (Mail::failures()) {
                        $output['success'] = $msg.' But the mail was not send.' ;
                    }
                }catch(Exception $e){
                     $output['error'] = $msg.' '.$e->getMessage() ;
                }
            }
        }
        return redirect()->back()->with('message_'.$type, $msg);

    }

     // For API,  Show the events list UI
    function manageEventsListApiUi(){
        
        $search_text = '';
        $list = Event::all();

        $html  = '';
        $html  .= '
             <div class="container">
                <h2 class="py-4">Events List</h2>
                
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        
                   </tr>
               </thead>
            <tbody>';
       
        
        if(isset($list) && count($list)){
            foreach($list as $event_info){
                $html .= '<tr>';
                $html .= '<td>'.$event_info->id.'</td>';
                $html .= '<td>'.$event_info->name.'</td>';
                $html .= '<td>'.$event_info->slug.'</td>';
                $edit_url = url('/events/').'/'.$event_info->id.'/edit';
                $html .= '</tr>';
            }

        }else{
            $html = "<tr class='text-center'><td colspan='3'>Data Not Found</td></tr>";
        }
              
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        echo  $html; die;

    }

    
}
