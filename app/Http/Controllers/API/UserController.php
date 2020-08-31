<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use App\Http\Requests\ResetValidation;
use Validator;
use File;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Illuminate\Support\Facades\Session;
class UserController extends Controller 
{
public $successStatus = 200;

/** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 


    private function respondWithError($code, $message, $data)
  {
    return response()->json(array('code'=>$code,'message'=>$message,'data'=>$data));
  }

     public function login(Request $request){

                 if(is_numeric($request->get('email')))
         {

             $user = User::find(Auth::id());
             if(Auth::attempt(['mobile_no' => "+91".''.$request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            return response()->json(['result'=>'Login Successful','user_id'=>$user->id,'name'=>$user->name,'status'=>'1','token'=>$user->createToken('MyApp')-> accessToken], $this-> successStatus); 
        } 
        else{ 
             return response()->json(['error'=>'Invalid Username and Password!','status'=>'0'], 401); 
             
            } 
          }
          elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            $user = User::find(Auth::id());
    
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            return response()->json(['result'=>'Login Successful','name'=>$user->name,'user_id'=>$user->id,'status'=>'1','token'=>$user->createToken('MyApp')-> accessToken], $this-> successStatus); 
        } 
        else{ 
             return response()->json(['error'=>'Invalid Username and Password!','status'=>'0'], 401); 
             

        } 
          }






    }
/** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function registertwilio(Request $request) 
    { 
        
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email|unique:users', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
     if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        try
        {
        $email=$request->email;
        $password=bcrypt($request->password);
        $mobile_no=$request->mobile_no;
        $user_name=$request->username;
        $city=$request->city;
        $sex=$request->sex;
        $dob=$request->dob;
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $appSid     = config('app.twilio')['TWILIO_APP_SID'];
        $client = new Client($accountSid, $authToken);
        $otp = mt_rand(1000,9999);
        $message=$client->messages->create("+91" . ' ' .$mobile_no,array('from' => '+1 830 243 6586', 'body' => $otp,));
       $request->session()->put('otp', $otp);
       $request->session()->put('email', $email);
       $request->session()->put('user_name', $user_name);
       $request->session()->put('password', $password);
       $request->session()->put('mobile_no', $mobile_no);
       $request->session()->put('city', $city);
       $request->session()->put('sex', $sex);
       $request->session()->put('dob', $dob);
       return response()->json(['result'=>'Otp send successfully','status'=>'1','otp'=>$otp], $this-> successStatus);
        
    }
     catch (Exception $e)
        {
            echo "Something wrong";
        }
    }
    public function verifyOtptwilio(Request $request)
    {
        try
        {
           $otp=$request->Session()->get('otp');
           $user_name=$request->Session()->get('user_name');
           $email=$request->Session()->get('email');

           $password=$request->Session()->get('password');
           $mobile_no=$request->Session()->get('mobile_no');
           $city=$request->Session()->get('city');
           $sex=$request->Session()->get('sex');
           $dob=$request->Session()->get('dob');
           $user_otp=$request->otp;
         if($user_otp == $otp)
           {
         Session::forget('otp');
         $user=new User;
         $user->email=$email;
         $user->name=$user_name;
         $user->password=$password;
         $user->mobile_no='+91'.''.$mobile_no;
         $user->city=$city;
         $user->sex=$sex;
         $user->dob=$dob;
         if($user->save())
        {
           return response()->json(['result'=>'Register Successful','name'=>$user->name,'status'=>'1','token'=>$user->createToken('MyApp')-> accessToken], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised','status'=>'0'], 401);        }
       
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
    public function facebook(Request $request)
    {
        $email=$request->email;
        $name=$request->name;
        if(!$email)
        {
          return response()->json(['error'=>'Unauthorised','status'=>'0'], 401);

        }
        $user = User::where('email',$email)->first();
        $success['email'] =  $email;

        if($user == "")
         { 
            $email=$request->email;
            $user_data=new user;
            $user_data->email=$email;
            $user_data->name=$name;

            if($user_data->save())
            {           
          return response()->json(['id'=>$user_data->id,'status'=>'2'], $this-> successStatus); 


            } 
            else
            {
             return response()->json(['error'=>'Unauthorised','status'=>'0'], 401); 
            }

             
        } 
        else{ 
          return response()->json(['result'=>'Login Successful','user_id'=>$user->id,'name'=>$user->name,'status'=>'1','token'=>$user->createToken('MyApp')-> accessToken], $this-> successStatus); 
            
              } 
    

    }
 public function facebookprofl(Request $request)
    {
      $id=$request->id;
      $sex=$request->sex;
      $city=$request->city;
      $dob=$request->dob;
      $profile_data=User::where('id',$id)->first();
      $profile_data->sex=$sex;
      $profile_data->city=$city;
      $profile_data->dob=$dob;
      if($profile_data->save())
      {
       return response()->json(['result'=>'Login Successful','status'=>'1','user_id'=>$profile_data->id,'name'=>$profile_data->name,'token'=>$profile_data->createToken('MyApp')-> accessToken], $this-> successStatus); 
      }
      else
            {
             return response()->json(['error'=>'Unauthorised','status'=>'0'], 401); 
            }
    }
 public function register(Request $request) 
    { 
        
        $validator = Validator::make($request->all(), [  
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
     if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        try
        {
        $email=$request->email;
        $password=$request->password;
        $mobile_no='+91'.''.$request->mobile_no;
        $user_name=$request->username;
        $city=$request->city;
        $sex=$request->sex;
        $country=$request->country;
        $state=$request->state;
        $district=$request->district;
        $dob=$request->dob;
        $user_data=User::where('mobile_no', $mobile_no)->orWhere('email', $email)->first();
        if($user_data == "")
          {
           return response()->json(['status'=>'1','email'=>$email,'password'=>$password,'mobile_no'=>$request->mobile_no,'username'=>$user_name,'city'=>$city,'sex'=>$sex,'dob'=>$dob,'country'=>$country,'state'=>$state,'district'=>$district], $this-> successStatus); 
        
            }
       else 
       {
        return response()->json(['result'=>'Mobile number or email already exist!','status'=>'0'], 401);
          }
       
        
    }
     catch (Exception $e)
        {
            echo "Something wrong";
        }
    }
public function verified(Request $request) 
    { 
        
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email|unique:users', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
     if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        try
        {
        $email=$request->email;
        $password=$request->password;
        $mobile_no='+91'.''.$request->mobile_no;
        $user_name=$request->username;
        $city=$request->city;
        $sex=$request->sex;
        $country=$request->country;
        $state=$request->state;
        $district=$request->district;
        $dob=$request->dob;
         $user=new User;
         $user->email=$email;
         $user->name=$user_name;
         $user->password=bcrypt($password);
         $user->mobile_no=$mobile_no;
         $user->country=$country;
         $user->state=$state;
         $user->district=$district; 
         $user->city=$city;
         $user->sex=$sex;
         $user->dob=$dob;
         if($user->save())
        {
           return response()->json(['result'=>'Register Successful','user_id'=>$user->id,'name'=>$user->name,'status'=>'1','token'=>$user->createToken('MyApp')-> accessToken], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['result'=>'Unauthorised','status'=>'0'], 401);        }       
        
    }
     catch (Exception $e)
        {
            echo "Something wrong";
        }
    }

public function userprofile(Request $request)
{
  try{
    $user_id=$request->user_id;
    $file = $request->file('profile');
      if (!$file->isValid()) {
        return $this->respondWithError(400,"Invalid Content",array());
      }
  $uploadPath=public_path()."/userimage";
  $ext = explode('.',$file->getClientOriginalName());
   $extenstions = array(
        'image'=>array('jpg','jpeg','png'));  
   if (!in_array($ext[1], $extenstions['image'])) 
      {
        return response()->json(['result'=>'Not an image file!','status'=>'0'], 401);      }

      $filename = uniqid('media_').'_'.date('Y-m-d-h-i-s');
      $filename = $filename.'.'.$ext[1];
      $data = $file->move(trim($uploadPath), $filename);
      $userimg =User::where('id', $user_id)->first();;
      $userimg->media_path ="http://ec2-3-15-216-225.us-east-2.compute.amazonaws.com/public/userimage/".$filename;
      $userimg->save();
     return response()->json(['result'=>'Profile Updated successfully!','user_id'=>$userimg->id,'media_path'=>$userimg->media_path,'status'=>'1'], 401);}
catch (\Exception $e) {
      return $this->respondWithError(500,"Internal Server Error!",array());
    }

}



}