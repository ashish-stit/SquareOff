<?php

namespace App\Http\Controllers;
use App\sport;
use App\country;
use App\district;
use App\state;
use App\joincircle;
use Illuminate\Http\Request;
use App\TableTenniscreateCircle;
use App\joincircleresponse;
use Validator;
use App\User;
use DB;

class TableTennisController extends Controller
{
public $successStatus = 200;
private function respondWithError($code, $message, $data)
  {
    return response()->json(array('code'=>$code,'message'=>$message,'data'=>$data));
  }

    public function sport(Request $request)
    {
       try{
      $sport_data=sport::get();
        return response()->json(['status'=>'1','data' => $sport_data], $this-> successStatus); 
    }
   catch (\Exception $e) {
        return $this->respondWithError(500,"Internal Server Error!",array());
    }


    }
 public function teniscircle(Request $request)
    {         
      try{    

                  $user_id=$request->user_id;
                     $user_data=User::where('id',$user_id)->first();
                  if($user_data == "")
                   {
                     return response()->json(['result'=>'We can not find any user on that Id!','status'=>'0'], 401);
                   }
               else
                   {
                    if($user_data->is_circle == "1")
                    {
                      return response()->json(['result'=>'You have already created or member of circle','status'=>'0'], 401);
                    }
                   else
                       {
                     $circle_name=$request->circle_name;
                     $data=TableTenniscreateCircle::where('circle_name',$circle_name)->first();
                        if($data == "")
                     {
      $sport_id=$request->sport_id;
      $country=$request->country;
      $state=$request->state;
                        $district=$request->district;
      $area=$request->area;
                        $nextId = DB::table('table_tenniscreate_circles')->max('id') + 1;
                        $circle_data=new TableTenniscreateCircle();
                        $user_data->is_circle="1"; 
                        $user_data->is_create="1"; 
      $circle_data->user_id=$user_id;
      $circle_data->sport_id=$sport_id;
      $circle_data->circle_name=$circle_name;
      $country_data=country::where('id',$country)->first();
                        $circle_data->country=$country_data->country_name;; 
                        $state_data=state::where('id',$state)->first();
                        $circle_data->state=$state_data->name;
                        $circle_data->fcm_token=$user_data->fcm_token;
                        $district_data=district::where('id',$district)->first();
                        $circle_data->district=$district_data->name;
                        $circle_data->share_url="http://www.SquareOff.com/CircleRequestLinkScreen?user_id=".$user_id."&group_id=".$nextId."&sport_id=".$sport_id."&fcm_token=".$user_data->fcm_token;
                        $share_url="http://www.SquareOff.com/CircleRequestLinkScreen?user_id=".$user_id."&group_id=".$nextId."&sport_id=".$sport_id."&fcm_token=".$user_data->fcm_token;
      $circle_data->area=$area;
                        $circle_data->status="1";
                        $user_data->save();
      if($circle_data->save())
      {
               return response()->json(['status'=>'1','result'=>'Circle created successfully','id'=>$circle_data->id,'user_id'=>$user_id,'sport_id'=>$sport_id,'circle_name'=>$circle_name,'share_url'=>$share_url,'area'=>$area,'country'=>$country_data->country_name,'state'=>$state_data->name,'district'=>$district_data->name], $this-> successStatus);

      }
                   else{ 
                    return response()->json(['result'=>'Unauthorised','status'=>'0'], 401);        
       
                   }
                   }
                 else{ 
                    return response()->json(['result'=>'This circle name already exist','status'=>'0'], 401);        
       
                   }
               }
            }
       }
     catch (\Exception $e) {
echo $e->getmessage();

        return $this->respondWithError(500,"Internal Server Error!",array());
    }

    
      }
      public function showcircle(Request $request)
          {
            try{
              $sport_id=$request->sport_id;
             $user_id=$request->user_id;
             $response_data=DB::select('select circle_id from joincircleresponses where user_id ='.$user_id); 
             $res_data=TableTenniscreateCircle::where('sport_id',$sport_id)->orderBy('circle_name', 'ASC')->get();
             $er=joincircleresponse::where('user_id', $user_id)->pluck('circle_id')->toArray();
             if($response_data)
              {
                     $result_data =DB::table('table_tenniscreate_circles')
                    ->whereNotIn('id',$er)
                    ->orderBy('table_tenniscreate_circles.circle_name', 'ASC')
                    ->get();
            $response=DB::table('table_tenniscreate_circles')
        ->join('joincircleresponses', 'joincircleresponses.circle_id', '=', 'table_tenniscreate_circles.id')
        ->select('table_tenniscreate_circles.*','joincircleresponses.circle_response')
        ->where('joincircleresponses.user_id',$request->user_id)->orderBy('table_tenniscreate_circles.circle_name', 'ASC')
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
           public function country(Request $requset)
      {
          try{
         $country_data=country::get();
        return response()->json(['status'=>'1','data' => $country_data], $this-> successStatus);
        }
       catch (\Exception $e) {
        return $this->respondWithError(500,"Internal Server Error!",array());
    }

      }
    public function state(Request $request)
      {
     try{
         $state_data=state::get();
        return response()->json(['status'=>'1','data' => $state_data], $this-> successStatus);
       }
     catch (\Exception $e) {
        return $this->respondWithError(500,"Internal Server Error!",array());
    }

      }

     public function district(Request $request)
      {
     try
        {
          $state_id=$request->state_id;
         $district_data=district::where('state_id',$state_id)->get();
        return response()->json(['status'=>'1','data' => $district_data], $this-> successStatus);
      }
     catch (\Exception $e) {
        return $this->respondWithError(500,"Internal Server Error!",array());
    }

      }
     public function circlelist(Request $request)
          {
         try
            {
             $user_id=$request->user_id;
             $list_data=TableTenniscreateCircle::get();
             return response()->json(['status'=>'1','data' => $list_data], $this-> successStatus);
            }
     catch (\Exception $e) {
        return $this->respondWithError(500,"Internal Server Error!",array());
    }

            }
        public function singlecircle(Request $request)
          {
           try{
              $user_id=$request->user_id;
              $sport_id=$request->sport_id;
               $is_accept="1";
              $check_data=User::where('id',$user_id)->first();
       if($check_data->is_circle == "0")
       {
         return response()->json(['status'=>'0'], $this-> successStatus);

         }
       else
       {
          $single_data=TableTenniscreateCircle::where('user_id',$user_id)->where('sport_id',$sport_id)->first();
           if($single_data)
              {
             $member_data=DB::select('select * from joincircles where circle_id='.$single_data->id." and is_accept=".$is_accept." and sport_id=".$sport_id);
             $user_data=User::where('id',$single_data->user_id)->first();
             return response()->json(['status'=>'1','id' => $single_data->id,'user_id'=>$single_data->user_id,'sport_id'=>$single_data->sport_id,'circle_name'=>$single_data->circle_name,'user_name'=>$user_data->name,'country'=>$single_data->country,'state'=>$single_data->state,'district'=>$single_data->district,'area'=>$single_data->area,'share_url'=>$single_data->share_url,'member_data'=>$member_data], $this-> successStatus);
            
          }
           else
             {
                
                $join_data=DB::select('select * from joincircles where user_id='.$user_id." and is_accept=".$is_accept." and sport_id=".$sport_id);
                $single_data=TableTenniscreateCircle::where('user_id',$join_data[0]->admin_id)->first();
                  $member_data=DB::select('select * from joincircles where circle_id='.$single_data->id." and is_accept=".$is_accept." and sport_id=".$sport_id);
                 $user_data=User::where('id',$single_data->user_id)->first();
                  $user=User::where('id',$user_id)->first();
                return response()->json(['status'=>'1','id' => $single_data->id,'user_id'=>$single_data->user_id,'sport_id'=>$single_data->sport_id,'circle_name'=>$single_data->circle_name,'name'=>$user->name,'user_name'=>$user_data->name,'country'=>$single_data->country,'state'=>$single_data->state,'district'=>$single_data->district,'area'=>$single_data->area,'share_url'=>'','member_data'=>$member_data], $this-> successStatus);
           
              }
}
            }
     catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }

}




 }
