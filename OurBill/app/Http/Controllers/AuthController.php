<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function getEditProfile()
    {
        return view('auth.edit-profile', ['user' => auth()->user()]);
    }

    public function editProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'username' => 'required|string|alpha_num|max:255',
            'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if(auth()->user()->provider !== 'google'){
            if(!$request->has('password') || !$request->password){
                return back()->with('error', 'Password is required to update profile.');
            }
            if(!Hash::check($request->password, auth()->user()->password)){
                return back()->with('error', 'Invalid password. Please try again.');
            }
        }

        $isAvailable = true;

        $user = $request->user();
        if(User::where('username', '=', $request->username)->exists()){
            if($user){
                if($user->username !== $request->username){
                    $isAvailable = false;
                }
                else{
                    $isAvailable = true;
                }
            }
            else{
                $isAvailable = false;
            }
        }

        if(!$isAvailable){
            return back()->with('error', 'Username already taken. Please choose another one.');
        }

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        if($request->hasFile('profile_picture')){
            if($user->profile && Storage::disk('public')->exists($user->profile)) {

                Storage::disk('public')->delete($user->profile);
            }
            $image = $request->file('profile_picture');
            $filename = time() . '.' . $image->getClientOriginalName();
            $request->file('profile_picture')->storeAs('images', $filename, 'public');
            $user->profile = 'images/' . $filename;
        }
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
    public function getLogin()
    {
        return view('auth.login');
    }

    public function register(Request $request){
        if($request->password !== $request->password_confirmation){
            return back()->withErrors(['password' => 'Passwords do not match.']);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'username' => 'required|string|alpha_num|max:255|unique:users',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'provider' => 'local',
        ]);

        auth()->login($user);
        $request->session()->regenerate();
        return redirect()->route('dashboard')->with('success', 'Registration successful.');
    }

    public function login(Request $request) {
        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
      
        $credentials = [
          $field      => $login,
          'password'  => $request->password,
        ];

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Invalid credentials. Please try again.');
    }

    public function google() {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback() {
        try {
            $user = Socialite::driver('google')->user();

            $findUser = User::where('email', $user->getEmail())->first();
            if ($findUser) {
                auth()->login($findUser);
            } else {
                $username = explode('@', $user->getEmail())[0];
                $username = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
                $username = $username . rand(1, 1000);
                while (User::where('username', $username)->exists()) {
                    $username = $username . rand(1, 1000);
                }
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'username' => $username,
                    'provider' => 'google',
                ]);
                auth()->login($newUser);
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to authenticate with Google. Please try again.');
        }
    }


    public function getRegister()
    {
        return view('auth.register');
    }

    public function getForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function getLogout()
    {
        auth()->logout();
        return redirect()->route('login');  
    }

    public function checkUsername(Request $request){
        $request->validate([
            'username' => 'required|string|alpha_num|max:255',
        ]);
        $isAvailable = true;

        $user = $request->user();
        if(User::where('username', '=', $request->username)->exists()){
            if($user){
                if($user->username !== $request->username){
                    $isAvailable = false;
                }
                else{
                    $isAvailable = true;
                }
            }
            else{
                $isAvailable = false;
            }
        }

        return response()->json([
            'success' => $isAvailable,
        ]);
    }

    public function forgotPassword(Request $request) {
        if($request->password !== $request->password_confirmation){
            return back()->with(['error' => 'Passwords do not match.']);
        }
        $request->validate([
            'email' => 'required|email',
            'username' => 'required|string',
            'otp' => 'required|numeric',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $request->email)->where('username', $request->username)->first();
        if($user){
            $otp = Otp::where('user_id', $user->id)->where('otp', $request->otp)->where('expires_at', '>', now())->where('status', 'unused')->first();
            if($otp){
                $user->password = bcrypt($request->password);
                $user->save();
                $otp->status = 'used';
                $otp->save();
                return redirect()->route('login')->with('success', 'Password reset successfully. Please login.');
            }
            else{
                return back()->with(['error' => 'Invalid or expired OTP.']);
            }
        }
        else{
            return back()->with(['error' => 'User not found.']);
        }
    }
}
