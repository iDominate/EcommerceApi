<?php

namespace App\Http\Controllers;

use App\Enums\StatusCodeEnum;
use App\Models\Category;
use App\Models\Product;
use App\Traits\CategoryTrait;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    //
    use ResponseTrait;
    function __construct()
    {
        //$this->middleware('auth:api-admins')
    }

    public function all()
    {
        return $this->returnMessage(error: false,message: "All categories", data: Category::select(['id','name'])->get());
    }

    public function allWithProducts()
    {
        return $this->returnMessage(error: false,message: "All categories", data: Category::with('products')->get());
    }

    public function totalProfitByCategory()
    {
        $categories = Category::all();
        $categories->makeHidden('products');
        $result = collect();
        foreach($categories as $category){
            $result->push(['name'=>$category->name,'total_profit'=>$category->products->sum('total_profit').'$']);
        }

        return $this->returnMessage(error: false,message: "Total profit by category", data: $result);
    }

    public function create(Request $request)
    {
        $validation= Validator::make($request->all(), ["name"=>"required|alpha_num"]);

        if($validation->fails())
        {
            return $this->returnMessage(false, "Malformed entry", $validation->errors(), StatusCodeEnum::BadRequest);
        }

        

         $category = new Category();
         $category->name = $request->name;
         $category->created_by = ucfirst(auth()->user()->name);
         $category->number_of_products = 0;
         $category->save();

         return $this->returnMessage(false, "Category Created", $category, StatusCodeEnum::Created);
    }
    public function update(Request $request, $id = null)
    {
        $fields = $request->only(['name']);

        if(!$id)
        return $this->returnMessage(true, 'No id provided',null,StatusCodeEnum::BadRequest);
        if (!$fields) {
            return $this->returnMessage(true, 'No fields provided',null,StatusCodeEnum::BadRequest);
        }

        $category = Category::find($id);

        

        
        if(!$category)
        {
            return $this->returnMessage(true, 'No Category with such id : '.$id,null,StatusCodeEnum::BadRequest);
        }

        $category->update($fields);
        

        return $this->returnMessage(false,'Category Updated', $category, StatusCodeEnum::OK);
    }

    public function delete($id = null)
    {

        if(!$id)
        return $this->returnMessage(true, 'No id provided',null,StatusCodeEnum::BadRequest);
        $category = Category::find($id);


        if(!$category)
        return $this->returnMessage(true, 'No Category with such id : '.$id,null,StatusCodeEnum::BadRequest);

        $category->delete();

        return $this->returnMessage(false,'Category Deleted', ['deleted'=>$id,'categories'=>Category::all()],StatusCodeEnum::OK);


    }
}
