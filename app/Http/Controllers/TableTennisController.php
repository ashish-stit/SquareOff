<?php

namespace App\Http\Controllers;
use App\sport;
use App\country;
use App\district;
use App\state;
use App\joincircle;
use Illuminate\Http\Request;
use App\TableTenniscreateCircle;
use Validator;
use App\User;
use DB;

class TableTennisController extends Controller
{
public $successStatus = 200;
    public function sport(Request $request)
    {
    	$sport_data=sport::get();
        return response()->json(['status'=>'1','data' => $sport_data], $this-> successStatus); 

    }
 public function teniscircle(Request $request)
    {                $user_id=$request->user_id;
                     $user_data=User::where('id',$user_id)->first();
                  if($user_data == "")
                   {
                     return response()->json(['result'=>'We have not find any user on that Id!','status'=>'0'], 401);
                   }
               else
                   {
                    if($user_data->is_circle == "1")
                    {
                      return response()->json(['result'=>'You have already created a circle','status'=>'0'], 401);
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
			$circle_data->user_id=$user_id;
			$circle_data->sport_id=$sport_id;
			$circle_data->circle_name=$circle_name;
			$country_data=country::where('id',$country)->first();
                        $circle_data->country=$country_data->country_name;; 
                        $state_data=state::where('id',$state)->first();
                        $circle_data->state=$state_data->name;
                        $district_data=district::where('id',$district)->first();
                        $circle_data->district=$district_data->name;
                        $circle_data->share_url="http://www.SquareOff.com/CircleRequestLinkScreen?user_id=".$user_id."&group_id=".$nextId;
                        $share_url="http://www.SquareOff.com/CircleRequestLinkScreen?user_id=".$user_id."&group_id=".$nextId;
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
         public function showcircle(Request $request)
          {
             $sport_id=$request->sport_id;
             $circle_data=TableTenniscreateCircle::where('sport_id',$sport_id)->get();
             return response()->json(['status'=>'1','data' => $circle_data], $this-> successStatus);
            }
      public function country(Request $requset)
      {
         $country_data=country::get();
        return response()->json(['status'=>'1','data' => $country_data], $this-> successStatus);
      }
    public function state(Request $request)
      {
         $state_data=state::get();
        return response()->json(['status'=>'1','data' => $state_data], $this-> successStatus);
      }

     public function district(Request $request)
      {
          $state_id=$request->state_id;
         $district_data=district::where('state_id',$state_id)->get();
        return response()->json(['status'=>'1','data' => $district_data], $this-> successStatus);
      }
     public function circlelist(Request $request)
          {
             $user_id=$request->user_id;
             $list_data=TableTenniscreateCircle::where('user_id',$user_id)->get();
             return response()->json(['status'=>'1','data' => $list_data], $this-> successStatus);
            }
        public function singlecircle(Request $request)
          {
             $circle_id=$request->circle_id;
             $single_data=TableTenniscreateCircle::where('id',$circle_id)->first();
            $member_data=DB::select('select * from joincircles where circle_id='.$request->circle_id." and is_accept="."1");
             $user_data=User::where('id',$single_data->user_id)->first();
             return response()->json(['status'=>'1','id' => $single_data->id,'user_id'=>$single_data->user_id,'sport_id'=>$single_data->sport_id,'user_name'=>$user_data->name,'circle_name'=>$single_data->circle_name,'country'=>$single_data->country,'state'=>$single_data->state,'district'=>$single_data->district,'area'=>$single_data->area,'share_url'=>$single_data->share_url,'member_data'=>$member_data], $this-> successStatus);
            }




 }
<?php
namespace App\Http\Controllers;
use App\User;
use App\joincircle;
use App\turnament;
use App\turnamentuser;
use App\TableTenniscreateCircle;
use DB;

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
   public function turnamentuserlist(Request $request)
    {
       try{
       $circle_id=$request->circle_id;
       $name_data=DB::select('select id,user_id,admin_id,circle_id,user_name from joincircles where circle_id ='.$circle_id);
       return response()->json(['status'=>'1','data' => $name_data], $this-> successStatus);
       }
     catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }


    }
public function createturnament(Request $request)
{
try{
     $turnament_name=$request->turnament_name;
     $circle_id=$request->circle_id;
     $admin_id=$request->admin_id;
     $user_id=array("1", "2", "3");
     $turnament = new turnament;
     $turnament->admin_id=$admin_id;
     $turnament->circle_id=$circle_id;
     $turnament->turnament_name=$turnament_name;
     $meal_ingredients = array();
     $turnament->save();
     $count = 0;
        foreach($user_id as $ids)
   {
             $turnament_user = new turnamentuser;
             $count++;
             $turnament_user->turnament_name =$turnament_name;
             $turnament_user->turnament_admin_id = $admin_id;
             $turnament_user->user_id=$ids;
             $turnament_user->circle_id =$circle_id;
             $turnament_user->save();
   }
    return response()->json(['status'=>'1','data' => $turnament], $this-> successStatus);
}
 catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }

     
}

}
