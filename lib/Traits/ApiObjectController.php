<?php

namespace OCA\Biblio\Traits;


trait ApiObjectController {
    const MODEL_INCLUDE = 'model';

    /**
     * @param string $include
     * 
     * @return array
     */
    public function parseIncludesString(?string $include) {
        if(isset($include)) {
            $includes = array_filter(explode('+', $include));

            if(!!$includes) {
                return $includes;
            }
        }

        return [self::MODEL_INCLUDE];
    }

    /**
     * @param string $filter
     * 
     * @return array
     */
    public function parseFilterString(?string $filter): array {
        if(isset($filter)) {
            $parsed = json_decode($filter, true);

            if(!array_is_list($parsed)) {
                return array_filter($parsed, array($this, 'isValidFilter'));
            }
        }
        
        return [];
    }

    /**
     * @param array $filter
     * 
     * @return boolean
     */
    public function isValidFilter(array $filter) {
        return isset($filter["operator"]) && isset($filter["operand"]);
    }
}