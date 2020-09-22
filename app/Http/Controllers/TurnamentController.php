<?php
namespace App\Http\Controllers;
use App\User;
use App\joincircle;
use App\turnament;
use App\turnamentuser;
use App\TableTenniscreateCircle;
use App\jointurnament;
use App\jointurnamentresponse;
use DB;
use App\notification;  

use Illuminate\Http\Request;

class TurnamentController extends Controller
{
    private function respondWithError($code, $message, $data)
  {
    return response()->json(array('code'=>$code,'message'=>$message,'data'=>$data));
  }
   public $successStatus = 200;
  public function turnamentcirclelist(Request $request)
    {
         try
         {
             $list_data=DB::select('select id,user_id,sport_id,circle_name from table_tenniscreate_circles');
             return response()->json(['status'=>'1','data' => $list_data], $this-> successStatus);
         }
    catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }


    }

 public function mycirclelist(Request $request)
    {
         try
         {
             $user_id=$request->user_id; 
               $user_data=User::where('id',$user_id)->first();
             if($user_data->is_create==1)
               {
                $circle_name_data=DB::select('select * from table_tenniscreate_circles where user_id ='.$user_id);
                return response()->json(['status'=>'1','data' =>$circle_name_data], $this-> successStatus);

               }
             elseif($user_data->is_join==1)
               {
                 $join_list_data=DB::select('select circle_id from joincircles where user_id ='.$user_id);
                 $join_circle_name_data=DB::select('select * from table_tenniscreate_circles where id ='.$join_list_data[0]->circle_id);
                 return response()->json(['status'=>'1','data' =>$join_circle_name_data], $this-> successStatus);
               }
           else
             {
             }
             
           }
    catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }


    }
     public function turnamentuserlist(Request $request)
    {
       try{
       $circle_id=$request->circle_id;
       $name_data=Joincircle::where('circle_id',$circle_id)->get(); 
       return response()->json(['status'=>'1','data' => $name_data], $this-> successStatus);
       }
     catch (\Exception $e) {

      return $this->respondWithError(500,"Internal Server Error!",array());
    }


    }
    public function myuserlist(Request $request)
    {
       try{
       $circle_id=$request->circle_id;
       $name_data=Joincircle::where('circle_id',$circle_id)->get(); 
       return response()->json(['status'=>'1','data' => $name_data], $this-> successStatus);
       }
     catch (\Exception $e) {

      return $this->respondWithError(500,"Internal Server Error!",array());
    }


    }

public function sendInvothercircle(Request $request)
{
try{
     $admin_id=$request->admin_id;
      $table_data=TableTenniscreateCircle::where('user_id',$admin_id)->first(); 
      $join_data=Joincircle::where('user_id',$admin_id)->first(); 
      if($join_data=="" and $table_data=="")
        {
          return response()->json(['status'=>'0','result'=>'You can not create turnament first create any circle or join any circle'], $this-> successStatus);
        }
else
{
      $turnament_name=$request->turnament_name;
     $organizer=$request->organizer;
     $date=$request->date;
     $sport_id=$request->sport_id;
     $country=$request->country;
     $message=$request->message;
     $enum_type=$request->enum_type;
     $enum=$request->enum;
     $state=$request->state;
     $district=$request->district;
     $circle_id=$request->circle_id;
     $user=$request->user_id;
     $user_id = explode (",", $user);  
     $turnament = new turnament;
     $turnament->admin_id=$admin_id;
     $turnament->organizer=$organizer;
     $turnament->date=$date;
     $turnament->country=$country;
     $turnament->state=$state;
     $turnament->district=$district;
     $turnament->sport_id=$sport_id;
     $turnament->circle_id=$circle_id;
     $turnament->turnament_name=$turnament_name;
     $meal_ingredients = array();
     $turnament->save();
     $count = 0;
        foreach($user_id as $ids)
   {
             $turnament_user = new turnamentuser;
             $count++;
             $turnament_user->turnament_id =$turnament->id;
             $turnament_user->turnament_name =$turnament_name;
             $turnament_user->turnament_admin_id = $admin_id;
             $turnament_user->user_id=$ids;
             $turnament_user->circle_id =$circle_id;
             $turnament_user->save();
             $notification_data=new notification();
             $notification_data->user_id=$ids;
             $notification_data->enum=$enum;
             $notification_data->sport_id=$sport_id;
             $notification_data->enum_type=$enum_type;
             $notification_data->message=$message;
             $notification_data->save();


   }
    return response()->json(['status'=>'1','data' => $turnament], $this-> successStatus);
}
}
 catch (\Exception $e) {

      return $this->respondWithError(500,"Internal Server Error!",array());
    }

     
}
public function sendInvmycircle(Request $request)
{
try{
     $admin_id=$request->admin_id;
      $table_data=TableTenniscreateCircle::where('user_id',$admin_id)->first(); 
      $join_data=Joincircle::where('user_id',$admin_id)->first(); 
      if($join_data=="" and $table_data=="")
        {
          return response()->json(['status'=>'0','result'=>'You can not create turnament first create any circle or join any circle'], $this-> successStatus);
        }
else
{
     $turnament_name=$request->turnament_name;
     $organizer=$request->organizer;
     $date=$request->date;
     $sport_id=$request->sport_id;
     $country=$request->country;
     $state=$request->state;
     $district=$request->district;
     $circle_id=$request->circle_id;
     $user=$request->user_id;
     $user_id = explode (",", $user);  
     $message=$request->message;
     $enum_type=$request->enum_type;
     $enum=$request->enum;
     $turnament = new turnament;
     $turnament->admin_id=$admin_id;
     $turnament->organizer=$organizer;
     $turnament->date=$date;
     $turnament->country=$country;
     $turnament->state=$state;
     $turnament->district=$district;
     $turnament->sport_id=$sport_id;
     $turnament->circle_id=$circle_id;
     $turnament->turnament_name=$turnament_name;
     $meal_ingredients = array();
     $turnament->save();
     $count = 0;
        foreach($user_id as $ids)
   {
             $turnament_user = new turnamentuser;
             $count++;
             $turnament_user->turnament_id =$turnament->id;
             $turnament_user->turnament_name =$turnament_name;
             $turnament_user->turnament_admin_id = $admin_id;
             $turnament_user->user_id=$ids;
             $turnament_user->circle_id =$circle_id;
             $turnament_user->save();
              $notification_data=new notification();
             $notification_data->user_id=$ids;
             $notification_data->enum=$enum;
              $notification_data->sport_id=$sport_id;
             $notification_data->enum_type=$enum_type;
             $notification_data->message=$message;
             $notification_data->save();

   }
    return response()->json(['status'=>'1','data' => $turnament], $this-> successStatus);
}
}
 catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }

     
}
        public function turnamentlist(Request $request)
            {
            try{
              $sport_id=$request->sport_id;
             $user_id=$request->user_id;
             $response_data=DB::select('select turnament_id from jointurnamentresponses where user_id ='.$user_id); 
             $res_data=turnament::where('sport_id',$sport_id)->orderBy('turnament_name', 'ASC')->get();
             $er=jointurnamentresponse::where('user_id', $user_id)->pluck('turnament_id')->toArray();
             if($response_data)
              {
                     $result_data =DB::table('turnaments')
                    ->whereNotIn('id',$er)
                    ->orderBy('turnaments.turnament_name', 'ASC')
                    ->get();
            $response=DB::table('turnaments')
        ->join('jointurnamentresponses', 'jointurnamentresponses.turnament_id', '=', 'turnaments.id')
        ->select('turnaments.*','jointurnamentresponses.turnament_response')
        ->where('jointurnamentresponses.user_id',$request->user_id)->orderBy('turnaments.turnament_name', 'ASC')
        ->get();
       return response()->json(['status'=>'1','response_data' => $response,'data'=>$result_data], $this-> successStatus);
         }
           else
          {
            return response()->json(['status'=>'1','data'=>$res_data], $this-> successStatus);
           }
      
             }

         catch (\Exception $e) 
       {
        echo $e->getmessage();
        return $this->respondWithError(500,"Internal Server Error!",array());
        }
      }




      public function turnamentrequest(Request $request)
      {
       try
        {
       $user_id=$request->user_id;
      $user_data=User::where('id',$user_id)->first();
     if($user_data == "")
      {
       return response()->json(['result'=>'We can not find any user in that id','status'=>'0'], 401);
      }
      else
      {

       $turnament_id=$request->turnament_id;
       $turnament_admin_id=turnament::where('id',$turnament_id)->first();
       $message=$request->message;
       $enum_type=$request->enum_type;
       $enum=$request->enum;
       $turnament_request=$request->turnament_request;
       $turnament_response=$request->turnament_response;
       $sport_id=$request->sport_id;
       $for_response_data=new jointurnamentresponse();  
       $turnament_user_data=DB::select('select id,admin_id from turnaments where id ='.$turnament_id." and admin_id=".$user_id);
        if($turnament_user_data)
       {
           return response()->json(['result'=>'You are admin in this turnament','status'=>'0'], 401);
        }
      else
       {
         $turnament_data=new jointurnament();
         $turnament_data->user_id=$user_id;
         $turnament_data->turnament_id=$turnament_id;
         $turnament_data->sport_id=$sport_id;
         $turnament_data->turnament_request=$turnament_request;
         $turnament_data->user_name=$user_data->name;
         $turnament_data->fcm_token=$user_data->fcm_token;
         $for_response_data->turnament_id=$turnament_id;
         $for_response_data->user_id=$user_id;
         $for_response_data->turnament_response=$turnament_response;
         $notification_data=new notification();
         $notification_data->user_id=$turnament_admin_id->admin_id;
         $notification_data->turnament_id=$turnament_id; 
         $notification_data->sport_id=$sport_id;
         $notification_data->enum=$enum;
         $notification_data->enum_type=$enum_type;
         $notification_data->message=$message;
        if($turnament_data->save())
        {
           $for_response_data->save();
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
      public function turnamentaccept(Request $request)
      {
      try{
      $user_id=$request->user_id;
      $is_circle=$request->is_circle;
      $message=$request->message;
      $admin_id=$request->admin_id;
      $is_accept=$request->is_accept;
      $enum=$request->enum; 
      $enum_type=$request->enum_type;
      $turnament_id=$request->turnament_id;
      $response_data=User::where('id',$user_id)->first();
      if($response_data == "")
      {
        return response()->json(['result'=>'We can not find that user','status'=>'0'], 401);
      }
      else{
      if($is_accept == 0)
      {
      $response_res=jointurnament::where('user_id',$user_id)->first();
       $notification_data=new notification();
       $notification_data->user_id=$user_id;
       $notification_data->turnament_id=$turnament_id; 
       $notification_data->enum=$enum;
       $notification_data->enum_type=$enum_type;
       $notification_data->message=$message;
       $response_res->delete();
       $notification_data->save();
      return response()->json(['result'=>'Admin not accept your request','status'=>'0'], 401);
      
      }
      else
    {
      $user_id=$request->user_id;
      $message=$request->message;
      $admin_id=$request->admin_id;
      $is_accept=$request->is_accept;
      $turnament_id=$request->turnament_id;
      $notification_data=new notification();
         $notification_data->user_id=$user_id;
         $notification_data->turnament_id=$turnament_id; 
         $notification_data->enum=$enum;
         $notification_data->enum_type=$enum_type;
         $notification_data->message=$message;
         $response=jointurnament::where('user_id',$user_id)->where('turnament_id',$turnament_id)->first();
         $response->admin_id=$admin_id;
         $response->is_accept=$is_accept;
         $response_data->save();
         if($response->save())
         {
          $notification_data->save();
          
           return response()->json(['status'=>'1','result'=>'You join turnament successfully'], $this-> successStatus);
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
