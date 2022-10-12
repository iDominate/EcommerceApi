<?php

namespace App\Enums;

enum StatusCodeEnum : int {
    case OK = 200;
    case BadRequest = 400;
    case NotFound = 404;
    case Created = 201;
    case Unauthenticated = 401;
    case Forbidden = 403;
}