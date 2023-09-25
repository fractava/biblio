<?php

namespace OCA\Biblio\Helper\ApiObjects;

use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\IAppContainer;

abstract class AbstractObjectHelper {
    const MODEL_INCLUDE = 'model';

    /**
     * @var IAppContainer
     */
    protected IAppContainer $container;

    /** @var string */
	private $userId;


    /**
     * AbstractObjectHelper constructor.
     *
     * @param IAppContainer $container
     * @param string $userId
     */
    public function __construct(
        IAppContainer $container,
        $userId
    ) {
        $this->container = $container;
        $this->userId = $userId;
    }

    /**
     * @param string $include
     * 
     * @return array
     */
    public function parseIncludesString(string $include) {
        return explode('+', $include);
    }

    /**
     * @param string $test
     * @param array $includes
     * 
     * @return boolean
     */
    public function shouldInclude(string $test, array $includes) {
        return in_array($test, $includes);
    }

    /**
     * @param $entity
     * @param string|null $include
     *
     * @return array|null
     */
    abstract public function getApiObject($entity, ?string $include): ?array;

    public function getApiObjects($entities, ?string $include): ?array {
        if(!isset($include)) {
            $include = self::MODEL_INCLUDE;
        }

        $result = [];
		foreach($entities as $entity) {
			$object = $this->getApiObject($entity, $include);

			if($object !== null) {
				$result[] = $object;
			}
		}

        return $result;
    }
}