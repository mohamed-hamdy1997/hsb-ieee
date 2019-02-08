<?php

namespace App\Http\Controllers\AuthApi;

use App\Ex_com_options;
use App\HighBoardOptions;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller as Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use JWTAuthException;
use App\User;
use App\Post;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'invalid_email_or_password',
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'failed_to_create_token',
            ]);
        }
        return response()->json([
            'response' => 'success',
            'result' => [
                'token' => $token,


                
            ],
        ]);
    }

    public function register(Request $request)
    {
        $this->validate($request ,[
            'firstName' => 'required |string | max:50 | min:5',
            'lastName' => 'required |string | max:50 | min:5',
            'faculty' => 'required |string',
            'university' => 'required |string',
//            'DOB' => 'date_format:Y-M-D|before:today',
            'email' => 'required |string|email|max:255| unique:users',
            'password'=>'required|confirmed|string|min:6',
            'password_confirmation'=>'sometimes|required_with:password',
        ]);
         //if position EX-com
        if ($request->input('position')=='EX-com') {$this->validate($request, ['EX-comOptions' => 'required']);}

        //if position High board and the committee was chosen RAS, PES, WIE:
        if ($request->input('position')=='highBoard'&& ($request->input('committee')=='RAS' || 'PES' || 'WIE'))
        {$this->validate($request, ['highBoardOptions' => 'required']);}

        $user= new User();
        $user->firstName= $request->input('firstName');
        $user->lastName= $request->input('lastName');
        $user->faculty= $request->input('faculty');
        $user->university= $request->input('university');
        $user->DOB= $request->input('DOB');
        $user->position= $request->input('position');
        $user->email=$request->input('email');
        $user->password=app('hash')->make($request->input('password'));

        if ($request->input('position')=='EX_com'){
            $ex = new Ex_com_options();
            $ex->ex_options = $request->input('ex_options');
            if ($ex->ex_options!=null){
                $user->save();
                $ex->ex_id = $user->id;
                $ex->save();
            }else{return response()->json('error');}
        }

        if ($request->input('position')=='highBoard' || 'volunteer'){
            $user->committee = $request->input('committee');
        }

        if ($request->input('position')=='highBoard' && ($request->input('committee')==('RAS'||'PES' || 'WIE') )){
            $hb = new HighBoardOptions();
            $hb->HB_options = $request->input('highBoardOptions');
            if ($hb->HB_options != null){
                $user->save();
                $hb->HB_id = $user->id;
                $hb->save();
            }else{return response()->json('error');}
        }

        if ($request->input('position')!='EX_com' && ($request->input('position')=='highBoard' && ($request->input('committee')==('RAS'||'PES' || 'WIE') )))
        {

            $user->save();
        }

           return response()->json(['status' =>'success','user'=>$user]);
        }

//        Logout
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

 }
        


