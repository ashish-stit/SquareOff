<?php

namespace App\Http\Controllers;
use App\User;
use App\newgame;
use DB;
use App\notification;
use Illuminate\Http\Request;

class NewGameController extends Controller
{
public $successStatus = 200;
     private function respondWithError($code, $message, $data)
  {
    return response()->json(array('code'=>$code,'message'=>$message,'data'=>$data));
  }
   public function userlist(Request $request)
     {
       try
     {
       $circle_id=$request->circle_id;
       $is_accept="1";
       $list_data = DB::select('select id,user_id,user_name from joincircles where circle_id ='.$circle_id." and is_accept=".$is_accept);
       return response()->json(['status'=>'1','result' => $list_data], $this-> successStatus); 
     }
     catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }
     }
 public function gamerequest(Request $request)
     {
       try
     {
       $user_id=$request->user_id;
       $from_user_id=$request->from_user_id;
       $message=$request->message;
       $enum_type=$request->enum_type;
       $enum=$request->enum;
       $game_data=new newgame();
       $game_data->user_id=$user_id;
       $game_data->from_user_id=$from_user_id;
       $notification_data=new notification();
       $notification_data->user_id=$from_user_id; 
       $notification_data->enum=$enum;
       $notification_data->enum_type=$enum_type;
       $notification_data->message=$message;
       if($game_data->save())
       {
       $notification_data->save();
       return response()->json(['status'=>'1','result'=>'Request send successfully','data' => $game_data], $this-> successStatus); 
      }
     }
     catch (\Exception $e) {
        return $this->respondWithError(500,"Internal Server Error!",array());
    }
     }
       public function confirm(Request $request)
        {
  try{
         $from_user_id=$request->from_user_id;
         $user_id=$request->user_id;
         $response=$request->response;
         $message=$request->message;
         $enum_type=$request->enum_type;
         $enum=$request->enum;

         $rows =newgame::where('user_id',$user_id)->where('from_user_id',$from_user_id)->first();
         if($rows == "")
         {
          return response()->json(['status'=>'0','result'=>'We can not find any user!'], $this-> successStatus);
         }
        else
        {
         if($response == "1")
        {
         $points="1000";
         $user_data=User::where('id',$user_id)->first();
          $user_data->total_point=$user_data->total_point+$points; 
          $rows->point=$points;
          $notification_data=new notification();
         $notification_data->user_id=$from_user_id; 
        $notification_data->enum=$enum;
        $notification_data->enum_type=$enum_type;
        $notification_data->message=$message;

          if($rows->save())
           {
              $notification_data->save();
             $user_data->save();
             return response()->json(['status'=>'1','result'=>'Request confirmed!'], $this-> successStatus);
           }
           else
         {
          return response()->json(['status'=>'0','result'=>'Something went wrong!'], $this-> successStatus);
         }
      }
        else
         {
          return response()->json(['status'=>'0','result'=>'Request Rejected!'], $this-> successStatus);
          }
         
         }
     }
   catch (\Exception $e) {
        return $this->respondWithError(500,"Internal Server Error!",array());
    }

         }
        public function sendNotification()
       {  
        $fcm_token="cjK5UcnxQdu2wl_n2_WaPF:APA91bEq6Uc_CSTQbLBMgaUBh3mlSoM_zaJGJVm5_ZT5q0a0a980X5d7W2vcBwXVfYSQD0BZKPeCzsGxQSigKQL2VSlKo2-1YwykNEWfTE02WTUkKZhlP55g5fGmhG-A5KHQ5jTYimi3";
         $id="3";
        $title="test1";
        $message="test";
        $push_notification_key ="AAAA9LIXzzs:APA91bGprOjFWZ9K1Mu_fPjuPimGR88Z1iuWCIx3ktgQo41JbAd7FaLiUH7fLUTcwHPWye-xrldpgWkapLYa36T3e7A2U-jY5Dl9cd2YJeZwNf4CWSXH0Ku5TXusSvb1DjqBHX5Pqb71";    
        $url = "https://fcm.googleapis.com/fcm/send";            
        $header = array("authorization: key=" . $push_notification_key . "",
            "content-type: application/json"
        );    

        $postdata = '{
            "to" : "' . $fcm_token . '",
                "notification" : {
                    "title":"' . $title . '",
                    "text" : "' . $message . '"
                },
            "data" : {
                "id" : "'.$id.'",
                "title":"' . $title . '",
                "description" : "' . $message . '",
                "text" : "' . $message . '",
                "is_read": 0
              }
        }';

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);    
        curl_close($ch);
       return $result;
    }



}
