<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\joincircle;
use App\User;
use App\TableTenniscreateCircle;
use App\joincircleresponse;
use App\notification;
use DB;

class CircleRequestController extends Controller
{
   public $successStatus = 200;
    private function respondWithError($code, $message, $data)
  {
    return response()->json(array('code'=>$code,'message'=>$message,'data'=>$data));
  }
       public function shownotification(Request $request)
       {
          try{
          $user_id=$request->user_id;
          $all_message=notification::where('user_id',$user_id)->get(); 
          $count=count($all_message);
          return response()->json(['status'=>'1','count'=>$count,'result'=>$all_message], $this-> successStatus);
          }
         catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }

       }
    public function circlerequest(Request $request)
     {
     try{
       $user_id=$request->user_id;
      $user_data=User::where('id',$user_id)->first();
     if($user_data == "")
      {
       return response()->json(['result'=>'We can not find any user in that id','status'=>'0'], 401);
      }
     elseif($user_data->is_circle == "1")
         {
            return response()->json(['result'=>'You have already created or member of circle','status'=>'0'], 401);
          }
      else{
       $circle_id=$request->circle_id;
       $circle_admin_id=TableTenniscreateCircle::where('id',$circle_id)->first();
       $message=$request->message;
       $enum_type=$request->enum_type;
       $enum=$request->enum;
       $circle_request=$request->circle_request;
       $circle_response=$request->circle_response;
       $sport_id=$request->sport_id;
       $for_response_data=new joincircleresponse();
       $circle_user_data=DB::select('select id,user_id from table_tenniscreate_circles where id ='.$circle_id." and user_id=".$user_id);
        if($circle_user_data)
       {
           return response()->json(['result'=>'You are admin in this circle','status'=>'0'], 401);
        }
      else
       {
        $response_data=joincircle::where('user_id',$user_id)->first();
         $circle_data=new joincircle();
         $circle_data->user_id=$user_id;
         $circle_data->circle_id=$circle_id;
         $circle_data->sport_id=$sport_id;
         $circle_data->circle_request=$circle_request;
         $circle_data->user_name=$user_data->name;
         $circle_data->fcm_token=$user_data->fcm_token;
         $for_response_data->circle_id=$circle_id;
         $for_response_data->user_id=$user_id;
         $for_response_data->circle_response=$circle_response;
         $notification_data=new notification();
         $notification_data->user_id=$circle_admin_id->user_id;
         $notification_data->circle_id=$circle_id; 
         $notification_data->enum=$enum;
         $notification_data->enum_type=$enum_type;
         $notification_data->message=$message;
         $for_response_data->save();
        if($circle_data->save())
        {
           $notification_data->save();
           return response()->json(['status'=>'1','result'=>'Request send  successfully'], $this-> successStatus);
         }
          } 
         
        }
      }
     catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }

      }



       public function urlrequest(Request $request)
     {
      try{
      $user_id=$request->user_id;
      $admin_id=$request->admin_id;
      $is_accept=$request->is_accept;
      $circle_id=$request->circle_id;
      $sport_id=$request->sport_id;
      $circle_request=$request->circle_request;
      $is_accept="1";
      $response=DB::select('select id from joincircles where user_id ='.$user_id." and is_accept=".$is_accept." and circle_id=".$circle_id);
      $user_data=User::where('id',$user_id)->first();
       if($user_data == "")
      {
       return response()->json(['result'=>'We can not find any user in that id','status'=>'0'], 401);
      }
    else 
    {
     if($user_data->is_circle== 1)
      { 
          return response()->json(['result'=>'You have already created or member of circle','status'=>'0'], 401);        
       
       }
    else

       {
         $response_data=new joincircle;
         $response_data->admin_id=$admin_id;
         $response_data->is_accept=$is_accept;
         $response_data->user_id=$user_id;
         $response_data->sport_id=$sport_id;
         $response_data->circle_id=$circle_id;
         $response_data->user_name=$user_data->name;
         $response_data->fcm_token=$user_data->fcm_token;
         $user_data->is_join="1"; 
         $user_data->is_circle="1";
         $user_data->save();
         if($response_data->save())
         {
           return response()->json(['status'=>'1','result'=>'You join circle successfully'], $this-> successStatus);
         } 
         else
             { 
                    return response()->json(['result'=>'Something wrong','status'=>'0'], 401);        
       
               }


       }

    }
  }
  catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }


       }
      public function circlerequestlist(Request $request)
         {
        try{
          $circle_id=$request->circle_id;
          $is_accept="0";
          $response_data=DB::select('select id,user_id,user_name from joincircles where circle_id ='.$circle_id." and is_accept=".$is_accept);
           return response()->json(['status'=>'1','result'=>$response_data], $this-> successStatus);
      }
    catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }


         }
    public function isaccept(Request $request)

    {
  try{
      $user_id=$request->user_id;
      $is_circle=$request->is_circle;
      $message=$request->message;
      $admin_id=$request->admin_id;
      $is_accept=$request->is_accept;
      $enum=$request->enum; 
      $enum_type=$request->enum_type;
      $circle_id=$request->circle_id;
       $response_data=User::where('id',$user_id)->first();
      if($response_data == "")
      {
        return response()->json(['result'=>'We can not find that user','status'=>'0'], 401);
      }
     else{
      if($is_accept == 0)
      {
      $response_res=joincircle::where('user_id',$user_id)->first();
      $response_res->delete();
      return response()->json(['result'=>'Admin not accept your request','status'=>'0'], 401);
      
      }
      elseif($response_data->is_circle == "1")
      { 
          return response()->json(['result'=>'Member already join any circle','status'=>'0'], 401);        
       
       }
     else

       {
      $user_id=$request->user_id;
      $is_circle=$request->is_circle;
      $message=$request->message;
      $admin_id=$request->admin_id;
      $is_accept=$request->is_accept;
      $circle_id=$request->circle_id;
      $notification_data=new notification();
         $notification_data->user_id=$user_id;
         $notification_data->circle_id=$circle_id; 
         $notification_data->enum=$enum;
         $notification_data->enum_type=$enum_type;
         $notification_data->message=$message;
          $response=joincircle::where('user_id',$user_id)->where('circle_id',$circle_id)->first();
         $response->admin_id=$admin_id;
         $response->is_accept=$is_accept;
         $response_data->is_circle="1";
         $response_data->is_join="1";
         $response_data->save();
         if($response->save())
         {
          $notification_data->save();
          
           return response()->json(['status'=>'1','result'=>'You join circle successfully'], $this-> successStatus);
         } 
         else
             { 
                    return response()->json(['result'=>'Something wrong','status'=>'0'], 401);        
       
               }


       }
       }
     }
 catch (\Exception $e) {
echo $e->getmessage();
      return $this->respondWithError(500,"Internal Server Error!",array());
    }


     }
}
