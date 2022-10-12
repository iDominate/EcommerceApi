<?php

namespace App\Traits;

use App\Enums\StatusCodeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidationTrait {

    use ResponseTrait;
    public function validateRequest(Request $request, $rules, $errorMessage = null)
    {
        $validation = Validator::make($request->all(), $rules);
        

        return $validation;
    }
}