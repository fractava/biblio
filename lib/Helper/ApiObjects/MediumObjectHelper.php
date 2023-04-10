<?php

namespace OCA\Biblio\Helper\ApiObjects;

use OCA\Biblio\Db\Medium;
use OCA\Biblio\Service\MediumService;
use OCA\Biblio\Service\MediumFieldService;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\IAppContainer;

class MediumObjectHelper extends AbstractObjectHelper {
    const TITLE_INCLUDE = 'model';
    const FIELDS_INCLUDE = 'fields';

    /** @var MediumService */
	private $mediumService;

	/** @var MediumFieldService */
	private $fieldService;

    /**
     * MediumObjectHelper constructor.
     *
     * @param IAppContainer $container
     * @param MediumService $mediumService,
     * @param MediumFieldService $fieldService,
     * @param string $userId
     */
    public function __construct(
        IAppContainer $container,
        MediumService $mediumService,
		MediumFieldService $fieldService,
        $userId
    ) {
        parent::__construct($container, $userId);

        $this->mediumService = $mediumService;
        $this->fieldService = $fieldService;
    }

    /**
     * @param Entity|Medium $entity
     * @param string|null $include
     *
     * @return array|null
     */
    public function getApiObject(
        Entity $entity,
        string $include = self::MODEL_INCLUDE,
    ): ?array {
        $includes = $this->parseIncludesString($include);

        $result = [
			'id' => $entity->getId(),
        ];

        if($this->shouldInclude(self::MODEL_INCLUDE, $includes) || $this->shouldInclude(self::TITLE_INCLUDE, $includes)) {
            $result->title = $entity->getTitle();
        }

        if($this->shouldInclude(self::FIELDS_INCLUDE, $includes)) {
            $result->fields = this->getFields();
        }

        return $result;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws DoesNotExistException
     */
	public function getFields(int $id) {
        return $this->fieldService->findAll($id);
	}

}