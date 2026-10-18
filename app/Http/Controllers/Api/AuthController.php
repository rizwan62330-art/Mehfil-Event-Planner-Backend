<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller {
 public function register(Request $request) {
  $data=$request->validate(['name'=>'required|string|min:2|max:120','email'=>'required|email|max:255|unique:users,email','password'=>'required|string|min:8|confirmed']);
  $user=User::create(['name'=>$data['name'],'email'=>strtolower($data['email']),'password'=>$data['password'],'role'=>'customer']);
  return response()->json(['user'=>$user->only('id','name','email','role'),'token'=>$user->createToken('web')->plainTextToken],201);
 }
 public function login(Request $request) {
  $data=$request->validate(['email'=>'required|email','password'=>'required|string']);
  $user=User::where('email',strtolower($data['email']))->first();
  if(!$user||!Hash::check($data['password'],$user->password)) throw ValidationException::withMessages(['email'=>['Invalid email or password.']]);
  return response()->json(['user'=>$user->only('id','name','email','role'),'token'=>$user->createToken('web')->plainTextToken]);
 }
 public function me(Request $request) { return response()->json(['user'=>$request->user()->only('id','name','email','role')]); }
 public function logout(Request $request) { $request->user()->currentAccessToken()->delete(); return response()->json(['message'=>'Signed out.']); }
}
