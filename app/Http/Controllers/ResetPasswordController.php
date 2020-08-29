<?php

namespace App\Http\Controllers;
use App\Http\Requests\ResetValidation;
use Illuminate\Http\Request;
use Hash;
use Auth;
use App\User;
use Validator;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller
{
   public $successStatus = 200;
   public function changepassword(Request $request)
{
    $input = $request->all();
    $userid = $request->id;
    $data=User::where('id', $userid)->first();
        $rules = array(
        'old_password' => 'required',
        'new_password' => 'required|min:6',
        'confirm_password' => 'required|same:new_password',
    );
    $validator = Validator::make($input, $rules);
    if ($validator->fails()) {
        $arr = array("status" => 0, "message" => $validator->errors()->first(), "data" => array());
    } else {
        try {
            if ((Hash::check(request('old_password'), $data->password)) == false) {
                $arr = array("status" => 0, "message" => "Check your old password.", "data" => array());
            } else if ((Hash::check(request('new_password'), $data->password)) == true) {
                $arr = array("status" => 0, "message" => "Please enter a password which is not similar then current password.", "data" => array());
            } else {
                User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                $arr = array("status" => 1, "message" => "Password updated successfully.", "data" => array());
            }
        } catch (\Exception $ex) {
            if (isset($ex->errorInfo[2])) {
                $msg = $ex->errorInfo[2];
            } else {
                $msg = $ex->getMessage();
            }
            $arr = array("status" => 0, "message" => $msg, "data" => array());
        }
    }
    return \Response::json($arr);
}
 public function forgetotp(Request $request)
    {

      $validator = Validator::make($request->all(), [ 
            'mobile_no' => 'required', 
             
        ]);
     if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        try{
        $mobile_no=$request->mobile_no;
         $user = User::where('mobile_no', '+91'.''.$request->mobile_no)->first();
        if (!$user)
            return response()->json([
                 'message' => 'We can not find a user on that mobile no'
              ]);
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $appSid     = config('app.twilio')['TWILIO_APP_SID'];
        $client = new Client($accountSid, $authToken);
        $otp = mt_rand(1000,9999);
        $message=$client->messages->create('+91'.''.$mobile_no,array('from' => '+1 830 243 6586', 'body' => $otp,));
                   $request->session()->put('otp', $otp);
                   $request->session()->put('mobile_no', $mobile_no);
                   return response()->json(['result'=>'Otp send successfully','otp'=>$otp,'status'=>'1','mobile_no'=>$mobile_no], $this-> successStatus); 
        
      }
      catch (Exception $e)
        {
            $arr = array("status" => 0, "message" => "Unauthorised", "data" => array());        }
         
    }
 public function resetotpverify(Request $request)
    {
        try
        {
           $otp=$request->Session()->get('otp');
           $mobile_no=$request->Session()->get('mobile_no');
           $user_otp=$request->otp;
            if($user_otp == $otp)
           {
                   Session::forget('otp');
                   return response()->json(['result'=>'Your mobile no verify successfully','mobile_no'=>$mobile_no,'status'=>'1'], $this-> successStatus); 

               
           }
           else{ 
            return response()->json(['error'=>'Unauthorised','status'=>'0'], 401); 
        } 
          
        }
        catch (Exception $e)
        {
            echo "Something wrong";
        }
    }
     public function resetPass(Request $request)
    {
        try
        {
           $mobile_no=$request->mobile_no;
          $user_data=User::where('mobile_no','+91'.''.$mobile_no)->first();
            if($user_data)
           {
                User::where('mobile_no', '+91'.''.$mobile_no)->update(['password' => Hash::make($request->password)]);
                 return response()->json(['result'=>'Your password reset successfully','user_id'=>$user_data->id,'status'=>'1'], $this-> successStatus); 

               
           }
           else{ 
            return response()->json(['result'=>'Unauthorised','status'=>'0'], 401); 
        } 
          
        }
        catch (Exception $e)
        {
            echo "Something wrong";
        }
    }
     

     

}
