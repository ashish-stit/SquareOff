<?php

namespace App\Http\Controllers;
use App\TableTenniscreateCircle;
use App\User;
use App\joincircle;
use App\challenge;
use Illuminate\Http\Request;
use DB;

class ChallengeController extends Controller
{
 private function respondWithError($code, $message, $data)
  {
    return response()->json(array('code'=>$code,'message'=>$message,'data'=>$data));
  }
   public $successStatus = 200;
   public function challengecirclelist(Request $request)
     {
       try
     {
       $challenge_data=TableTenniscreateCircle::select('id','user_id','circle_name')->get();
       return response()->json(['status'=>'1','data' => $challenge_data], $this-> successStatus); 
     }
     catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }
     }
   public function challengeuserlist(Request $request)
     {
     try
      {
       $circle_id=$request->circle_id;
      $name_data = joincircle::where(['circle_id' => $circle_id, 'is_accept' => "1"])->get();
       return response()->json(['status'=>'1','data' => $name_data], $this-> successStatus);
      }
     catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }
     }
   public function challengerequest(Request $request)
   {
     $circle_id=$request->circle_id;
     $user_id=$request->user_id;
     $challenge_data=new challenge();
     $challenge_data->circle_id=$circle_id;
     $challenge_data->user_id=$user_id; 
    if($challenge_data->save())
     {
       return response()->json(['status'=>'1','result' =>"Challenge request send successfully!" ], $this-> successStatus);
     }
    else
      {
         return response()->json(['result'=>'Something wrong','status'=>'0'], 401);
      }
   }
  public function challengeaccept(Request $request)
  {
    $user_id=$request->user_id;
    $circle_id=$request->circle_id;
    $accept_user_id=$request->accept_user_id;
    $is_accept=$request->is_accept;
    $challenge_data=challenge::where('user_id',$user_id)->orWhere('circle_id',$circle_id)->first();
    if($challenge_data == "")
     {
        return response()->json(['result'=>'We can not find any user!','status'=>'0'], 401);

      }  
  else
  {
   if($is_accept == "0")
   {
     return response()->json(['result'=>'Challenge Reject!','status'=>'0'], 401);
   }
   else
  {
   $challenge_data->is_accept=$is_accept;
   $challenge_data->accept_user_id=$accept_user_id;

   if($challenge_data->save())
    {
      return response()->json(['status'=>'1','result' =>"Challenge accepted!" ], $this-> successStatus);
    }
   else
   {
     return response()->json(['result'=>'Something wrong','status'=>'0'], 401);

   }
  }
 }
   
   }

}
