<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case BANK = 'bank';
    case UPI = 'upi';
    case CARD = 'card';
    case CHEQUE = 'cheque';
}