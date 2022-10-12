<?php

namespace App\Http\Controllers;

use App\Enums\StatusCodeEnum;

use App\Models\Category;
use App\Models\Product;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Traits\ProductTrait;
use App\Traits\ValidationTrait;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ResponseTrait, ProductTrait, ValidationTrait;
    //
    

    
    

    
   
    public function all()
    {
        return $this->returnMessage(false, "All Products", Product::all(), StatusCodeEnum::OK);
    }
    public function allForUsers()
    {
        return $this->returnMessage(false, "All Products", Product::select(['name', 'rate','unit_price']), StatusCodeEnum::OK);
    }

    public function searchForUsers($key, $value)
    {
        $vaildation = $this->ValidateProductKeysForUsers($key);

        if(!$vaildation)
        {
            return $this->returnMessage(true, "Invalid Fields", null, StatusCodeEnum::BadRequest);
        }

        $product = Product::where($key, $value)->select(['name', 'rate', 'unit_price'])->first();
        return $product;
        if(!$product)
        {
            return $this->returnMessage(true, "Invalid Field", null, StatusCodeEnum::BadRequest); 
        }

        return $this->returnMessage(false, "Searching Products by".$key." : ".$value, $product, StatusCodeEnum::OK);
    }

    public function OrderByForUsers($key, $order)
    {
        if(!$this->ValidateProductKeys($key))
        return $this->returnMessage(false, 'Invalid Information',null, StatusCodeEnum::BadRequest);

        $product = Product::orderBy($key, $order)->select(['name', 'rate', 'unit_price'])->get();
        if(!$product)
        return $this->returnMessage(false, 'No such Product', null, StatusCodeEnum::NotFound);
        
        return $this->returnMessage(false, 'Ordering Product by '.$key.' : '.$order, $product, StatusCodeEnum::OK);
    }
    


    public function searchBy($key, $value)
    {
        if(!$this->ValidateProductKeys($key))
        return $this->returnMessage(false, 'Invalid Information',null, StatusCodeEnum::BadRequest);

        $product = Product::where($key, $value)->get();
        if(!$product)
        return $this->returnMessage(false, 'No such Product', null, StatusCodeEnum::NotFound);
        
        return $this->returnMessage(false, 'Searching Product by: '.$key.' equals '.$value, $product, StatusCodeEnum::OK);
    }

    public function orderBy($key, $order)
    {
        if(!$this->ValidateProductOrder($key, $order))
        return $this->returnMessage(false, 'Invalid Order', null, StatusCodeEnum::OK);
        
        $product = Product::orderBy($key, $order)->get();
        
        
        return $this->returnMessage(false, 'Searching Product: '.$key.' ==> '.$order,$product, StatusCodeEnum::OK);
    }

    public function create(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'name'=>'required',
            'rate'=>'required|numeric',
            'unit_price'=>'required|numeric',
            'in_stock'=>'required|numeric',
            'sold'=>'required|numeric',
            'total_profit'=>'required|numeric',
            'remaining_inventory_in_aggergate'=>'required|numeric',
            'category_id'=>'required|numeric|exists:categories,id',
        ]);

        

        if($validation->fails()){
            return $this->returnMessage(false, "Invalid Fields", Category::select(['id'])->get(), StatusCodeEnum::BadRequest);
        }
        $category = Category::find($request->category_id);
        if(!$category)
        {
            return $this->returnMessage(false, "Invalid Category", Category::select(['id'])->get(), StatusCodeEnum::BadRequest);
        }
        

        
        
        Product::create($validation->validated());
        

        $category = Category::find($request->category_id)->first();
        $category->number_of_products++;
        $category->save();

        return $this->returnMessage(false, "Product created successfully", ['categoy'=>$category, 'product'=> Product::where('name', $request->name)->first()], StatusCodeEnum::Created);
    }   
    public function update(Request $request)
    {
        
        
        $validation = $this->validateRequest($request, [
            'name'=>'required',
            'rate'=>'required|numeric',
            'unit_price'=>'required|numeric',
            'in_stock'=>'required|numeric',
            'sold'=>'required|numeric',
            'total_profit'=>'required|numeric',
            'remaining_inventory_in_aggergate'=>'required|numeric',
            'category_id'=>'required|numeric|exists:categories,id',
        ]);
        if($validation->fails())
        {
            return $this->returnMessage(false, "Invalid Fields", $validation->errors(), StatusCodeEnum::BadRequest);
        }

        $product = Product::where('id', $request->id)->first();
        

        

        if(!$product)
        {
            return $this->returnMessage(false, "Invalid Product", ['hint'=>'Available Products', 'products'=>Product::select(['id', 'name'])->get()], StatusCodeEnum::BadRequest);
        }

        $product->update($validation->validated());
        $product->save();

        

        return $this->returnMessage(false, "Product updated successfully", ['categoy'=>Category::where('id', $product->category_id)->get(), 'product'=> Product::where('name', $product->name)->first()], StatusCodeEnum::Created);

    }

    public function delete(Request $request){
        $product = Product::where('id', $request->id)->first();
        

        

        if(!$product)
        {
            return $this->returnMessage(true, "Invalid Product", ['hint'=>'Available Products', 'products'=>Product::select(['id', 'name'])->get()], StatusCodeEnum::BadRequest);
        }

        

        $product->delete();
        return $this->returnMessage(false, "Deleted Successfully", null, StatusCodeEnum::OK);


    }

    


    
}

