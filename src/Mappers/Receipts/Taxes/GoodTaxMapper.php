<?php

namespace khoroshun\Checkbox\Mappers\Receipts\Taxes;

use khoroshun\Checkbox\Models\Receipts\Taxes\GoodTax;

class GoodTaxMapper
{
    /**
     * @param mixed $json
     * @return GoodTax|null
     */
    public function jsonToObject($json): ?GoodTax
    {
        if (is_null($json)) {
            return null;
        }

        $tax = new GoodTax(
            $json['id'],
            $json['code'],
            $json['label'],
            $json['symbol'],
            $json['rate'],
            $json['extra_rate'] ?? null,
            $json['included'],
            $json['created_at'],
            $json['updated_at'] ?? null,
            $json['value'] ?? 0,
            $json['extra_value'] ?? ''
        );

        return $tax;
    }
}
