<?php

namespace OCA\Biblio\Traits;


trait ApiObjectController {
    const MODEL_INCLUDE = 'model';

    /**
     * @param string $include
     * 
     * @return array
     */
    public function parseIncludesString(string $include) {
        $includes = array_filter(explode('+', $include));

        if(!$includes) {
            return [self::MODEL_INCLUDE];
        } else {
            return $includes;
        }
    }

    /**
     * @param string $filter
     * 
     * @return array
     */
    public function parseFilterString(string $filter): array {
        $parsed = json_decode($filter, true);

        if(!array_is_list($parsed)) {
            return $parsed;
        } else {
            return [];
        }
    }
}