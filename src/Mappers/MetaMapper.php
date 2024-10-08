<?php

namespace khoroshun\Checkbox\Mappers;

use khoroshun\Checkbox\Models\Meta;

class MetaMapper
{
    /**
     * @param mixed $json
     * @return Meta|null
     */
    public function jsonToObject($json): ?Meta
    {
        if (is_null($json)) {
            return null;
        }

        $meta = new Meta(
            $json['limit'],
            $json['offset']
        );

        return $meta;
    }
}
