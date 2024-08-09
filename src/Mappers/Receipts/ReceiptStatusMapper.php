<?php

namespace khoroshun\Checkbox\Mappers\Receipts;

use khoroshun\Checkbox\Models\Receipts\ReceiptStatus;

class ReceiptStatusMapper
{
    /**
     * @param mixed $json
     * @return ReceiptStatus|null
     */
    public function jsonToObject($json): ?ReceiptStatus
    {
        if (is_null($json)) {
            return null;
        }

        $receipt = new ReceiptStatus($json);

        return $receipt;
    }
}
