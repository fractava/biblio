<?php

namespace OCA\Biblio\Traits;


trait ApiObjectService {
    const MODEL_INCLUDE = 'model';

    /**
     * @param string $test
     * @param array $includes
     * 
     * @return boolean
     */
    public function shouldInclude(string $test, array $includes) {
        return in_array($test, $includes);
    }
}