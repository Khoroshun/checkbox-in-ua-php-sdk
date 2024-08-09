<?php

namespace khoroshun\Checkbox\Mappers\Reports;

use khoroshun\Checkbox\Mappers\MetaMapper;
use khoroshun\Checkbox\Mappers\Shifts\ZReportMapper;
use khoroshun\Checkbox\Models\Reports\Reports;

class ReportsMapper
{
    /**
     * @param mixed $json
     * @return Reports|null
     */
    public function jsonToObject($json): ?Reports
    {
        if (is_null($json)) {
            return null;
        }

        $shiftsArr = [];

        foreach ($json['results'] as $jsonRow) {
            $report = (new ZReportMapper())->jsonToObject($jsonRow);

            if (!is_null($report)) {
                $shiftsArr[] = $report;
            }
        }

        $meta = (new MetaMapper())->jsonToObject($json['meta']);

        $shift = new Reports(
            $shiftsArr,
            $meta
        );

        return $shift;
    }
}
