<?php

namespace khoroshun\Checkbox\Mappers\Receipts;

use khoroshun\Checkbox\Models\Receipts\Delivery;

class DeliveryMapper
{
    /**
     * @param Delivery $delivery
     * @return array<string, mixed>
     */
    public function objectToJson(Delivery $delivery): array
    {
        $output = [];

        if (!empty($delivery->emails())) {
            $output['emails'] = $delivery->emails();
        }

        if (!empty($delivery->phone())) {
            $output['phone'] = $delivery->phone();
        }

        return $output;
    }
}
