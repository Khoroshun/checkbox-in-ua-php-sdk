<?php

namespace khoroshun\Checkbox\Models\Reports;

use khoroshun\Checkbox\Models\Meta;
use khoroshun\Checkbox\Models\Shifts\ZReport;

class Reports
{
    /** @var array<ZReport> $results */
    public $results;
    /** @var Meta|null $meta */
    public $meta;

    /**
     * Constructor
     *
     * @param array<ZReport> $reports
     * @param Meta|null $meta
     *
     */
    public function __construct(array $reports, ?Meta $meta)
    {
        foreach ($reports as $report) {
            if (!is_a($report, ZReport::class)) {
                throw new \Exception('This is not zreport class');
            }

            $this->results[] = $report;
        }

        $this->meta = $meta;
    }
}
