<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordForm;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        return view('front.account.login');
    }
    public function register()
    {
        return view('front.account.register');
    }

    public function processRegister(Request $request) {

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:10',
            'email' => 'required|email|unique:users',
            'password' =>'required|min:8|confirmed'
        ]);
        if($validator->passes()){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash:: make($request->password);
            $user->save();
            session()->flash('success','You have been registered successfully.');
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' =>'required'
        ]);
        if($validator->passes()){
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password],$request->get('remember'))){
                if (session()->has('url.intended')) {
                    return redirect(session()->get('url.intended'));
                 }
                return redirect()->route('account.profile');
            } else {
                session()->flash('error','Either email/password is incorrect.');
                return redirect()->route('account.login')->withInput($request->only('email'));
            }
            // $user = new User;
            // $user->name = $request->name;
            // $user->email = $request->email;
            // $user->phone = $request->phone;
            // $user->password = Hash:: make($request->password);
            // $user->save();
            // session()->flash('success','You have been registered successfully.');
            // return response()->json([
            //     'status' => true,
        } else {
            // return response()->json([
            //     'status' => false,
            //     'errors' => $validator->errors()
          return redirect()->route('account.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }

    public function profile() {
        $user = User::where('id',Auth::user()->id)->first();
        return view('front.account.profile',[
            'user' => $user
        ]);
    }
    public function updateProfile(Request $request){
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userId.',id',
            'phone' =>'required'
        ]);
        if($validator->passes()){
            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();
            session()->flash('success','Your profile has been updated successfully.');
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function logout() {
        Auth::logout();
        return redirect()->route('account.login')->with('success','You have been successfully logged out.');
    }
    public function showChangePasswordForm() {
        return view('front.account.change-password');
    }
    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(),[
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' =>'required|same:new_password'
        ]);
        if($validator->passes()){
            $user = User::select('id','password')->where('id',Auth::user()->id)->first();
            if(!Hash::check($request->old_password, $user->password)) {
                session()->flash('success','Your old password is incorrect, please try again.');
                return response()->json([
                    'status' => true,
                ]);
            }
            User::where('id',$user->id)->update([
                'password' => Hash::make($request->new_password)
            ]);
            session()->flash('success','Your have changed your password successfully.');
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function forgotPassword() {
        return view('front.account.forgot-password');
    }

    public function processForgetPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email'
        ]);
        if($validator->fails()){
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }
        $token = Str::random(60);

        DB::table('password_reset_tokens')->where('email',$request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        //send mail
        $user = User::where('email',$request->email)->first();
        $formData = ['token' => $token, 'user' => $user, 'mailSubject' => 'You have requested to reset your password.'];
        Mail::to($request->email)->send(new ResetPasswordForm($formData));
        return redirect()->route('front.forgotPassword')->with('success','Please check your inbox to reset your password.');
    }
    public function resetPassword($token){
       $tokenExist = DB::table('password_reset_tokens')->where('token',$token)->first();
       if($tokenExist == null ){
        return redirect()->route('front.forgotPassword')->with('error','Invalid Request.');
       }
        return view('front.account.reset-password',['token' => $token]);
    }
    public function processResetPassword(Request $request) {
        $token = $request->token;
        $tokenObj = DB::table('password_reset_tokens')->where('token',$token)->first();
       if($tokenObj == null ){
        return redirect()->route('front.forgotPassword')->with('error','Invalid Request.');
       }
       $user = User::where('email',$tokenObj->email)->first();

       $validator = Validator::make($request->all(),[
        'new_password' => 'required|min:8',
        'confirm_password' => 'required|same:new_password'
    ]);
    if($validator->fails()){
        return redirect()->route('front.resetPassword',$token)->withErrors($validator);
    }
    User::where('id',$user->id)->update([
        'password' => Hash::make($request->new_password)
    ]);
    DB::table('password_reset_tokens')->where('email',$user->email)->delete();

    return redirect()->route('account.login')->with('success','You have successfully updated your password.');

    }
}
