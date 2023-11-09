<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use illuminate\http\JsonResponse;

class RegisterController extends BaseController
{
    public function register(Request $request):JsonResponse{
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'password'=>'required',
            'c_password'=>'required|same:password',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.',$validator->$errors());
        }
        $input=$request->all();
        $input['password']=bcrypt($input['password']);
        $user=User::create($input);
        $success['token']=$user->createToken('MyApp')->accessToken();
        $success['name']=$user->name;

        return $this->sendResponse($success,'User Register Success');
    }
    public function login(Request $request):JsonResponse{
        if(Auth::attempt(['email'=>$request->email,'passport'=>$request->passport])){
            $user=Auth::user();
            $success['token']=$user->createToken('MyApp')->accessToken();
            $success['name']=$user->name;

            return $this->sendResponse($success,'User Login Success');
        }
        else{
            return $this->sendError('Unautherized.',['error'=>'Unauthorised']);
        }
    }
}