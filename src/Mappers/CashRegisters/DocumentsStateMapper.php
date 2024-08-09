<?php

namespace khoroshun\Checkbox\Mappers\CashRegisters;

use khoroshun\Checkbox\Models\CashRegisters\DocumentsState;

class DocumentsStateMapper
{
    /**
     * @param mixed $json
     * @return DocumentsState|null
     */
    public function jsonToObject($json): ?DocumentsState
    {
        if (is_null($json)) {
            return null;
        }

        $state = new DocumentsState(
            $json['last_receipt_code'],
            $json['last_report_code'],
            $json['last_z_report_code']
        );

        return $state;
    }
}
