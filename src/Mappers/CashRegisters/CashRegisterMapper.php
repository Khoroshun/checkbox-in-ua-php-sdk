<?php

namespace khoroshun\Checkbox\Mappers\CashRegisters;

use khoroshun\Checkbox\Mappers\Shifts\ShiftMapper;
use khoroshun\Checkbox\Models\CashRegisters\CashRegister;

class CashRegisterMapper
{
    /**
     * @param mixed $json
     * @return CashRegister|null
     */
    public function jsonToObject($json): ?CashRegister
    {
        if (is_null($json)) {
            return null;
        }

        $shift = (new ShiftMapper())->jsonToObject($json['shift'] ?? null);

        $cashRegister = new CashRegister(
            $json['id'],
            $json['fiscal_number'],
            $json['created_at'],
            $json['updated_at'],
            $shift,
            $json['offline_mode'] ?? null
        );

        return $cashRegister;
    }
}
