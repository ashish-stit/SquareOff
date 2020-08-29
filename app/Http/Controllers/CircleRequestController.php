<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\joincircle;
use App\User;

class CircleRequestController extends Controller
{
   public $successStatus = 200;

    public function circlerequest(Request $request)
     {
       $user_id=$request->user_id;
      $user_data=User::where('id',$user_id)->first();
     if($user_data == "")
      {
       return response()->json(['result'=>'We can not find any user in that id','status'=>'0'], 401);
      }
      else{
       $circle_id=$request->circle_id;
       $circle_request=$request->circle_request;
       $response_data=joincircle::where('user_id',$user_id)->first();
         $circle_data=new joincircle();
         $circle_data->user_id=$user_id;
         $circle_data->circle_id=$circle_id;
         $circle_data->circle_request=$circle_request;
         $circle_data->user_name=$user_data->name;
        if($circle_data->save())
        {
           return response()->json(['status'=>'1','result'=>'Request send  successfully'], $this-> successStatus);
         }
           
         
        }
      }
    public function isaccept(Request $request)

    {
      $user_id=$request->user_id;
      $admin_id=$request->admin_id;
      $is_accept=$request->is_accept;
      $response_data=joincircle::where('user_id',$user_id)->first();
      if($response_data == "")
      {
        return response()->json(['result'=>'We can not find that user','status'=>'0'], 401);
      }
   else{
      if($is_accept == 0)
      {
      $response_data=joincircle::where('user_id',$user_id)->first();
      $response_data->delete();
      return response()->json(['result'=>'Admin not accept your request','status'=>'0'], 401);
      
      }
      elseif($response_data->is_accept == "1")
      { 
          return response()->json(['result'=>'Member already join any circle','status'=>'0'], 401);        
       
          }
     else

       {
         $response_data=joincircle::where('user_id',$user_id)->first();
         $response_data->admin_id=$admin_id;
         $response_data->is_accept=$is_accept;
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
}
