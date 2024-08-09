<?php

namespace khoroshun\Checkbox\Mappers\Receipts;

use khoroshun\Checkbox\Models\Receipts\ReceiptTypes;

class ReceiptTypeMapper
{
    /**
     * @param mixed $json
     * @return ReceiptTypes|null
     */
    public function jsonToObject($json): ?ReceiptTypes
    {
        if (is_null($json)) {
            return null;
        }

        $receipt = new ReceiptTypes($json);

        return $receipt;
    }
}
