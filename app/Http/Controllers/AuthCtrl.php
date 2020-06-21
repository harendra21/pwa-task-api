<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class AuthCtrl extends Controller
{
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login_user(Request $request){
        $formData     = $request->all();
        $email        = $formData['email'];
        $password     = $formData['password'];
        if(empty($email)){
          return array('status' => false,'msg' => 'Email is empty.','data' => null);
        }
        else if(empty($password)){
          return array('status' => false,'msg' => 'Password is empty.','data' => null);
        }
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          return array('status' => false,'msg' => 'Email is not valid.','data' => null);
        }
        else if (User::where('email',$email)->count() <= 0) {
          return array('status' => false,'msg' => 'This email is not registered.','data' => null);
        }

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return array('status' => false,'msg' => 'Email/Password is/are not correct','data' => null);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        //if ($request->remember_me)
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        $data = [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];

        return array('status' => true,'msg' => 'Successfully logged in.','data' => $data);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return array('status' => true,'msg' => 'Successfully logged out.','data' => null);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
