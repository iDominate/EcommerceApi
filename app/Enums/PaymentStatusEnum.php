<?php

namespace App\Enums;

enum PaymentStatusEnum: string {
    case Paid = "Paid";
    case PreparingOrder = "Preparing";
    case Shipping = "Shipping";
    case Shipped = "Shipped";
    case Delivered = "Delivered";
}