<?php

namespace App\Http\Controllers;

use App\Enums\StatusCodeEnum;
use App\Models\Cart;
use App\Models\NormalUser;
use App\Traits\ResponseTrait;

use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ImageTrait;
use App\Traits\ValidationTrait;
use Illuminate\Support\Facades\Validator;




class UserController extends Controller
{

    use ResponseTrait, ValidationTrait, ImageTrait;
    public function register(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'name'=>'required',
            'email'=>'required|unique:normal_users,email',
            'password'=>'required|max:64',
            "image"=>"required|image",
            'address'=>'required'
        ], "Invalid Fields");
        
        if($validation->fails())
        return $this->returnMessage(true,"Invalid Information",$validation->errors(),StatusCodeEnum::BadRequest);

        if(NormalUser::where("email", $request->email)->exists())
        {
            return $this->returnMessage(true, "User already exists", null, StatusCodeEnum::BadRequest);
        } 

        $result = NormalUser::create([
            'name'=> $request->name,
            'password'=> bcrypt($request->password),
            'email'=> $request->email,
            'address'=> $request->address,
            'image'=>$request->image
        ]);

        if(!$result)
        return $this->returnMessage(true,"Something went Wrong",null,StatusCodeEnum::BadRequest);
        $cart = new Cart();
        //INSERT LISTENER HERE
        $cart->name_of_owner = $request->name;
        $cart->email_of_owner = $request->email;
        $cart->number_of_products = 0;
        $cart->number_of_items = 0;
        $cart->user_id = $result->id;
        $cart->save();

        return $this->returnMessage(false,"User Created Successfully", NormalUser::where('name', $request->name)->get(), StatusCodeEnum::Created);

    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->only(['email','password']),['email'=>'required|exists:normal_users,email', 'password'=>'required']);
          if($validation->fails())
          return $this->returnMessage(true,"Test", $validation->errors(), StatusCodeEnum::BadRequest);

        
        try {
            $credentials = auth('api-normal-users')->attempt($validation->validated());

            return $this->returnMessage(false, "Logged in successfully",['accessToken'=>$credentials, 'user'=> auth()->guard('api-normal-users')->user()], StatusCodeEnum::OK);

        if(!$credentials)
        {
            return $this->returnMessage(true,"Wrong credentials",$validation->errors(),StatusCodeEnum::NotFound);
        }
            
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        

        
    }

    public function save(Request $request)
    {
        $body = $request->all();
        $this->saveImage($body['image']);
        return "Success";
    }

   
}
