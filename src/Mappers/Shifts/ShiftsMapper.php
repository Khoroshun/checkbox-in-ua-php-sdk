<?php

namespace khoroshun\Checkbox\Mappers\Shifts;

use khoroshun\Checkbox\Mappers\MetaMapper;
use khoroshun\Checkbox\Models\Shifts\Shifts;

class ShiftsMapper
{
    /**
     * @param mixed $json
     * @return Shifts|null
     */
    public function jsonToObject($json): ?Shifts
    {
        if (is_null($json)) {
            return null;
        }

        $shiftsArr = [];

        foreach ($json['results'] as $jsonRow) {
            $shift = (new ShiftMapper())->jsonToObject($jsonRow);

            if (!is_null($shift)) {
                $shiftsArr[] = $shift;
            }
        }

        $meta = (new MetaMapper())->jsonToObject($json['meta']);

        $shift = new Shifts(
            $shiftsArr,
            $meta
        );

        return $shift;
    }
}
