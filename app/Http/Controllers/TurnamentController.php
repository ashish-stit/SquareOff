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
