<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case AwaitingPayment = 'awaiting_payment';
    case Paid = 'paid';
    /** @deprecated Compatibilidad temporal con órdenes anteriores. */
    case DepositPaid = 'deposit_paid';
    case InProgress = 'in_progress';
    case AwaitingClient = 'awaiting_client';
    case Review = 'review';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
