<?php

namespace khoroshun\Checkbox\Mappers\Receipts;

use khoroshun\Checkbox\Mappers\Receipts\Payments\CashPaymentMapper;
use khoroshun\Checkbox\Models\Receipts\Payments\PaymentParent;
use khoroshun\Checkbox\Models\Receipts\ServiceReceipt;

class ServiceReceiptMapper
{
    /**
     * @param ServiceReceipt $receipt
     * @return array<string, array<string, int|string>|null>
     */
    public function objectToJson(ServiceReceipt $receipt): array
    {
        $payment = null;

        if ($receipt->payment->type == PaymentParent::TYPE_CASH) {
            $payment = (new CashPaymentMapper())->objectToJson($receipt->payment);
        } elseif ($receipt->payment->type == PaymentParent::TYPE_CARD) {
            $payment = (new CashPaymentMapper())->objectToJson($receipt->payment);
        }

        return [
            'payment' => $payment
        ];
    }
}
