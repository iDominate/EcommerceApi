<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatusEnum;
use App\Enums\StatusCodeEnum;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchasedProduct;
use App\Traits\ResponseTrait;

class OrderController extends Controller
{
    use ResponseTrait;
    
    public function checkout()
    {
        if(Order::where("user_id", auth()->user()->id)->exists())
        {
            return $this->returnMessage(true, "Order has already been placed", null, StatusCodeEnum::Forbidden);
        }
        $cart = Cart::where("user_id", auth()->user()->id)->first();
        $order = new Order();
        $order->name_of_owner = $cart->name_of_owner;
        $order->email_of_owner = $cart->email_of_owner;
        $order->user_id = $cart->user_id;
        $order->number_of_products = $cart->number_of_products;
        $order->number_of_items = $cart->number_of_items;
        $order->status = PaymentStatusEnum::Paid;
        $order->total_price = CartProduct::where("cart_id", $cart->id)->sum("total_price");
        $order->save();
        
         CartProduct::where("cart_id", $cart->id)->each(function($item, $key) use($order){
            $product = Product::where("name", join("_",explode(" ",$item->product_name)))->first();
            $product->in_stock -= $item->product_unit_count;
            $product->sold += $item->product_unit_count;
            $purchasedProduct = new PurchasedProduct();
            $purchasedProduct->product_name = $item->product_name;
            $purchasedProduct->product_quantity = $item->product_unit_count;
            $purchasedProduct->total_price = $item->total_price;
            $purchasedProduct->order_id = $order->id;
            $product->total_profit+=$item->total_profit;

            $product->remaining_inventory_in_aggergate -= $product->unit_price * $product->in_stock;
            $purchasedProduct->order_id = $order->id;
            $purchasedProduct->save();
            $product->save();
            
        });
        
        CartProduct::where("cart_id", $cart->id)->each(function($product, $key){
            $product->delete();
         });
         $cart->status = "empty";

         $cart->save();
         

        
        return $this->returnMessage(false, "Order has been placed successfully",
         Order::with("purchased_products")->where("user_id", auth()->user()->id)->get(),
          StatusCodeEnum::Created);
    }

    public function changeStatus(Request $request)
    {
        $data = $request->only(["status","id"]);
        
        if(!$data)
        {
            return $this->returnMessage(true, "Malformed entry", null, StatusCodeEnum::BadRequest);
        }
        $order = Order::where("id", $request->id)->first();
        
        $forbiddenStatusToChange = array("Paid", "Preparing");
        
        if(!in_array($order->status, $forbiddenStatusToChange) and in_array($request->status, $forbiddenStatusToChange))
        {
            return $this->returnMessage(true, "Cannot change status", null, StatusCodeEnum::Forbidden);
        }
        if(!in_array($request->status, array("Paid", "Preparing", "Shipping","Shipped", "Delivered")))
        {
            return $this->returnMessage(false, "Invalid Status", null, StatusCodeEnum::NotFound);
        }
        $order->status = $request->status;
        $order->save();

        return $this->returnMessage(false, "Status changed", $order, StatusCodeEnum::OK);
        
    }
}
