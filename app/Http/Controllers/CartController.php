<?php

namespace App\Http\Controllers;

use App\Enums\StatusCodeEnum;
use App\Http\Requests\CartRequest;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Traits\ResponseTrait;



class CartController extends Controller
{
    use ResponseTrait;
    public function __construct()
    {
        $this->middleware('auth:api-normal-users');
    }

    public function all()
    {
        
        if(!Cart::where('user_id', auth()->user()->id)->exists()) return $this->returnMessage(false,
         "An error has occurred",
          null, 
          StatusCodeEnum::OK);
    
        return $this->returnMessage(false, "Your Cart", Cart::with('cartProducts')->where('user_id', auth()->user()->id)->get(), StatusCodeEnum::OK);
    }

    public function addToCart(CartRequest $cartRequest)
    {
        
        $userCart = Cart::where('user_id', auth()->user()->id)->first();
        $existingProduct = CartProduct::where([["cart_id", $userCart->id], ["product_name", $cartRequest->product_name]])->exists();
        
        if($existingProduct)
        {
            return $this->returnMessage(true, "Product: {$cartRequest->product_name} Already Exists", null, StatusCodeEnum::BadRequest);
        }
        $cartProduct = new CartProduct();
        $cartProduct->product_name = $cartRequest->product_name;
        $cartProduct->product_unit_count = $cartRequest->product_unit_count;
        $cartProduct->unit_price = $cartRequest->unit_price;
        $cartProduct->total_price = $cartProduct->unit_price * $cartProduct->product_unit_count;
        
        $cartProduct->cart_id = $userCart->id;

        
        $cartProduct->save();
        
        $userCart->number_of_products ++;
        $userCart->status = "Used";
        $userCart->number_of_items += $cartProduct->product_unit_count;
        
        $userCart->save();

        return $this->returnMessage(false, "Product Added", Cart::with("cartProducts")->where("id", $userCart->id)->get(), StatusCodeEnum::Created);
    }

    public function updateProduct(CartRequest $cartRequest)
    {
        if(!$cartRequest->by)
        {
            return $this->returnMessage(true, "Missing Fields", "By is missing", StatusCodeEnum::BadRequest);
        }
        if(!$cartRequest->increment_or_decrement)
        {
            return $this->returnMessage(true, "Missing Fields", "increment_or_decrement Is Missing", StatusCodeEnum::BadRequest);
        }else if(!$cartRequest->by){
            return $this->returnMessage(true, "Missing Fields", "by is missing", StatusCodeEnum::BadRequest);
        }    
        $userCart = Cart::where('user_id', auth()->user()->id)->first(); 
        $product = CartProduct::where([["cart_id",$userCart->id], ["product_name", $cartRequest->product_name]])->first();
        
        if($cartRequest->incremnt_or_decrement = "inc")
        {
            $product->product_unit_count += $cartRequest->by;
            $userCart->number_of_items += $cartRequest->by;
        }else {
            $product->product_unit_count -= $cartRequest->by;
            $userCart->number_of_items += $cartRequest->by;
        }

        $product->save();
        $userCart->save();
        return $this->returnMessage(false, "Product Updated", $product, StatusCodeEnum::OK);

    }
    public function removeProduct($name)
    {
        $userCart = Cart::where('user_id', auth()->user()->id)->first(); 
        $product = CartProduct::where("product_name",$name)->first();
        $userCart->number_of_products--;
        if(!$userCart->number_of_products)
        {
            $userCart->status = "empty";
        }else if(!$product)
        {
            return $this->returnMessage(true, "Product: {$name} Doesn't Exist", null, StatusCodeEnum::BadRequest);
        }
        $userCart->number_of_items -= $product->product_unit_count;
        $userCart->save();
        $product->delete();
        

        return $this->returnMessage(false, "Product Deleted", Cart::with("cartProducts")->get(), StatusCodeEnum::OK);
    }

    public function removeAllProducts()
    {
        $userCart = Cart::where('user_id', auth()->user()->id)->first();
         CartProduct::where("cart_id",$userCart->id)->each(function($product, $key){
            $product->delete();
         });
         $userCart->status = "empty";
         $userCart->save();
        
        return $this->returnMessage(false, "Your cart is Empty", null, StatusCodeEnum::OK);
    }

}
