<?php

namespace khoroshun\Checkbox\Mappers\Receipts;

use khoroshun\Checkbox\Mappers\MetaMapper;
use khoroshun\Checkbox\Models\Receipts\Receipts;

class ReceiptsMapper
{
    /**
     * @param mixed $json
     * @return Receipts|null
     */
    public function jsonToObject($json): ?Receipts
    {
        if (is_null($json)) {
            return null;
        }

        $receiptsArr = [];

        foreach ($json['results'] as $jsonRow) {
            $receipt = (new ReceiptMapper())->jsonToObject($jsonRow);

            if (!is_null($receipt)) {
                $receiptsArr[] = $receipt;
            }
        }

        $meta = (new MetaMapper())->jsonToObject($json['meta']);

        $shift = new Receipts(
            $receiptsArr,
            $meta
        );

        return $shift;
    }
}
