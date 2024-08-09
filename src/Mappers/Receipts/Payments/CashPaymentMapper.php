<?php

namespace khoroshun\Checkbox\Mappers\Receipts\Payments;

use khoroshun\Checkbox\Models\Receipts\Payments\CashPaymentPayload;
use khoroshun\Checkbox\Models\Receipts\Payments\PaymentParent;

class CashPaymentMapper
{
    /**
     * @param mixed $json
     * @return CashPaymentPayload|null
     */
    public function jsonToObject($json): ?CashPaymentPayload
    {
        if (is_null($json)) {
            return null;
        }

        $receipt = new CashPaymentPayload(
            $json['value'],
            $json['label'] ?? ''
        );

        return $receipt;
    }

    /**
     * @param PaymentParent $obj
     * @return array<string, int|string>
     */
    public function objectToJson(PaymentParent $obj): array
    {
        $result = [
            'type' => $obj->type,
            'value' => $obj->value
        ];

        if (!empty($obj->label)) {
            $result['label'] = $obj->label;
        }

        return $result;
    }
}
