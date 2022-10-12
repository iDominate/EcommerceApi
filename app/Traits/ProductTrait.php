<?php
namespace app\Traits;



use Illuminate\Http\Request;
use App\Models\Product;

use App\Enums\StatusCodeEnum;

/**
 * 
 */
trait ProductTrait
{


    
    public function ValidateProductKeys($key)
    {
        $permittedProductKeys = [
            'name',
            'rate',
            'total_profit',
            'id'
        ];
        return in_array($key, $permittedProductKeys);
    }

    public function ValidateProductOrder($key, $order)
    {
        $permittedOrderValues = [
            'desc',
            'asc'
        ];

        $permittedProductKeys = [
            'name',
            'rate',
            'total_profit',
            'id'
        ];
        return in_array($key, $permittedProductKeys) && in_array($order, $permittedOrderValues);
    }

    public function ValidateProductKeysForUsers($key)
    {
        $permittedProductKeys = [
            'name',
            'rate',
            
            'unit_price'
        ];

        return in_array($key, $permittedProductKeys);
    }

    
    
}
