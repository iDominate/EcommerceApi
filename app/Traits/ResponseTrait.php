<?php

namespace App\Traits;
use App\Enums\StatusCodeEnum;



trait ResponseTrait {

    public function returnMessage($error = false ,$message, $data = null, $status = StatusCodeEnum::OK){
        return response()->json(
            [
                'errors'=> $error,
                'message'=> $message,
                'data'=> $data

            ]
            , $status->value
            );
    }
    }
