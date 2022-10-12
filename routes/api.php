<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware'=> 'api'], function(){
    /**
     * Begin Admin Area
     */
    Route::group(['prefix'=>'admin'], function(){
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::get('profile', [AdminAuthController::class, 'profile']);
    });


    /**
     * End Admin Area
     */
        //==============================================================================

    /**
     * Begin User Area
     */
    Route::group(['prefix'=>'user'], function(){
        Route::post('register', [UserController::class, 'register']);
        Route::post('login', [UserController::class, 'login']);
        Route::group(['prefix'=>'cart'], function(){
            
        });


    });

    /**
     * End User Area
     */

     //==============================================================================
    

    /**
     * Begin Product Area
     */
    Route::group(['prefix'=>'product'], function(){
        Route::group(['middleware'=>['auth:api-admins', 'auth.guard:api-admins']], function(){
        Route::get('/all', [ProductController::class, 'all']);
        
        Route::get('/search-by/{key}/{value}', [ProductController::class, 'searchBy']);
        Route::get('order-by/{key}/{order}', [ProductController::class, 'orderBy']);
        Route::post('create', [ProductController::class, 'create']);
        Route::post('update', [ProductController::class, 'update']);
        Route::post('delete', [ProductController::class, 'delete']);
        });

        
            Route::get('user/search-by/{key}/{value}', [ProductController::class, 'searchForUsers']);
            Route::get('user/order-by/{key}/{order}', [ProductController::class, 'orderByForUsers']);
        
    });

    /**
     * End Product Area
     */

     //==============================================================================

    /**
     * Begin Category Area
     */

    Route::group(['prefix'=>'category'], function(){
        Route::get('all', [CategoryController::class, 'all']);
        Route::get('all-with-products', [CategoryController::class, 'allWithProducts']);
        Route::group(['middleware'=>'auth:api-admins'], function(){
        Route::post('create', [CategoryController::class, 'create']);
        Route::post('update/{id?}', [CategoryController::class, 'update']);
        Route::post('delete/{id?}', [CategoryController::class, 'delete']);
        Route::get('profit', [CategoryController::class, 'totalProfitByCategory']);
        });
    });

    /**
     * End Category Area
     */

     //==============================================================================

    /**
     * Begin Cart Area
     */
    Route::group(['prefix'=>"cart"], function(){
        
        //INSERT EVENT LISTENERS
        Route::get('/all', [CartController::class, 'all']);
        Route::post('/remove/all', [CartController::class, 'removeAllProducts']);
        Route::post('/add', [CartController::class, 'addToCart']);
        
        Route::post('/update', [CartController::class, 'updateProduct']);
        Route::post('/remove/{name}', [CartController::class, 'removeProduct']);
    });

    /**
     * End Cart Area
     */

     //==============================================================================

    Route::group(['prefix'=>"order"], function(){
        Route::get("checkout", [OrderController::class,"checkout"])->middleware("auth:api-normal-users");
        Route::post("status", [OrderController::class,"changeStatus"])->middleware("auth:api-admins");
    });

    Route::post("images/save", [UserController::class, "save"]);
}); 