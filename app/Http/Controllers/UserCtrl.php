<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Intervention\Zodiac\Calculator as ZodiacCalculator;
class UserCtrl extends Controller
{
    //
    public function register_user(Request $request){

      if($request->hasFile('image')){
        $image = time().'.'.$request->image->extension();
        $request->image->move(public_path('uploads'), $image);
      }else{
        return array('status' => false,'msg' => 'Please select your profile image.','data' => null);
      }

      $formData     = $request->all();
      $f_name       = $formData['f_name'];
      $l_name       = $formData['l_name'];
      $gender       = $formData['gender'];
      $username     = $formData['username'];
      $email        = $formData['email'];
      $password     = $formData['password'];
      $c_password   = $formData['c_password'];
      $dob          = $formData['date'];

      if(empty($f_name)){
        return array('status' => false,'msg' => 'First name is empty.','data' => null);
      }
      else if(empty($l_name)){
        return array('status' => false,'msg' => 'Last name is empty.','data' => null);
      }
      else if(empty($gender)){
        return array('status' => false,'msg' => 'Gender is not selected.','data' => null);
      }

      else if(empty($username)){
        return array('status' => false,'msg' => 'Username is empty.','data' => null);
      }
      else if(strlen($username) <= 4){
        return array('status' => false,'msg' => 'Username must 5 digit long.','data' => null);
      }
      else if(empty($email)){
        return array('status' => false,'msg' => 'Email is empty.','data' => null);
      }
      else if(empty($password)){
        return array('status' => false,'msg' => 'Password is empty.','data' => null);
      }
      else if(strlen($password) <= 7){
        return array('status' => false,'msg' => 'Password must be 8 digit long.','data' => null);
      }
      else if($password !== $c_password){
        return array('status' => false,'msg' => 'Password are not matching.','data' => null);
      }
      else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return array('status' => false,'msg' => 'Email is not valid.','data' => null);
      }
      else if (User::where('username',$username)->count() > 0) {
        return array('status' => false,'msg' => 'This username is already exists.','data' => null);
      }
      else if (User::where('email',$email)->count() > 0) {
        return array('status' => false,'msg' => 'This email is already exists.','data' => null);
      }
      else {
        $zodiac = new ZodiacCalculator();
        $zodiac = $zodiac->make($dob); // virgo

        $addUser              = new User();
        $addUser->name        = $f_name.' '.$l_name;
        $addUser->f_name      = $f_name;
        $addUser->l_name      = $l_name;
        $addUser->dob         = $dob;
        $addUser->gender      = $gender;
        $addUser->sun_sign    = ucfirst($zodiac);
        $addUser->username    = $username;
        $addUser->email       = $email;
        $addUser->password    = Hash::make($password);
        $addUser->image       = $image;
        $isAdded              = $addUser->save();
        if($isAdded){
          return array('status' => true,'msg' => 'User added successfully !','data' => null);
        }
        return array('status' => false,'msg' => 'User not added. Please try again !','data' => null);
      }
    }

    public function get_users(Request $request){
      $data = $request->all();
      $name = $data['name'];
      $gender = $data['gender'];
      $sun_sign = $data['sun_sign'];
      $users = User::orderBy('id','DESC');
      if(!empty($name) && $name != 'null'){
        $users->where('name', 'LIKE', "%$name%");
      }
      if(!empty($gender) && $gender != 'null'){
        $users->where('gender',  $gender);
      }
      if(!empty($sun_sign) && $sun_sign != 'null'){
        $users->where('sun_sign',  $sun_sign);
      }
      if($users->count() > 0 ){
        $users = $users->get()->toArray();
      }else{
        $users = [];
      }

      $signs = User::select('sun_sign as sign')->groupBy('sun_sign')->get();

      return array('status' => true,'msg' => 'Data fetched.','data' => array('users' => $users,'signs' => $signs));

    }
}
