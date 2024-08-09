<?php

namespace khoroshun\Checkbox\Mappers\Shifts;

use khoroshun\Checkbox\Models\Shifts\Balance;

class BalanceMapper
{
    /**
     * @param mixed $json
     * @return Balance|null
     */
    public function jsonToObject($json): ?Balance
    {
        if (is_null($json)) {
            return null;
        }

        $balance = new Balance(
            $json['initial'],
            $json['balance'],
            $json['cash_sales'],
            $json['card_sales'],
            $json['cash_returns'],
            $json['card_returns'],
            $json['service_in'],
            $json['service_out'],
            $json['updated_at']
        );

        return $balance;
    }
}
