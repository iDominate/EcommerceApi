<?php

namespace App\Http\Controllers;

use App\Enums\StatusCodeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    use ResponseTrait;
    //
    function __construct()
    {
        $this->middleware('auth:api-admins', ['except'=> 'login']);
    }
    

    public function login(Request $request)
    {
        $rules = 
        [
            'email'=>'required',
            'password'=>'required'
        ];
        $validate = Validator::make($request->all(), $rules);

        
        if($validate->fails())
        {
            response()->json($this->returnMessage(true, $validate->errors(), status: StatusCodeEnum::BadRequest));
            
            
        }

        $credentials = auth()->guard('api-admins')->attempt($validate->validated());

        if(!$credentials)
        {
            return $this->returnMessage(true,"Wrong credentials",$validate->errors(),StatusCodeEnum::NotFound);
        }

        

        return $this->returnMessage(false, "Logged In Successfully", ['user'=>auth()->guard('api-admins')->user(),'accessToken'=> $credentials], StatusCodeEnum::OK);
    }

    public function profile()
    {
        return $this->returnMessage(error: false,message: "Admin Details", data: auth()->user(), status: StatusCodeEnum::OK);
    }

    public function logout()
    {
        auth()->logout();
        return $this->returnMessage(false, "Logged Out Successfully", null);
    }
}
