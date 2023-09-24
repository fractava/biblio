<?php

namespace OCA\Biblio\Helper\ApiObjects;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Service\ItemFieldService;
use OCA\Biblio\Service\ItemFieldValueService;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\IAppContainer;

class ItemObjectHelper extends AbstractObjectHelper {
    const TITLE_INCLUDE = 'model';
    const FIELDS_INCLUDE = 'fields';
    const FIELDS_ORDER_INCLUDE = 'fieldsOrder';

    /** @var ItemService */
	private $itemService;

	/** @var ItemFieldService */
	private $fieldService;

    /** @var ItemFieldValueService */
	private $fieldValueService;

    /**
     * ItemObjectHelper constructor.
     *
     * @param IAppContainer $container
     * @param ItemService $itemService
     * @param ItemFieldService $fieldService
     * @param ItemFieldValueService $fieldValueService
     * @param string $userId
     */
    public function __construct(
        IAppContainer $container,
        ItemService $itemService,
		ItemFieldService $fieldService,
        ItemFieldValueService $fieldValueService,
        $userId
    ) {
        parent::__construct($container, $userId);

        $this->itemService = $itemService;
        $this->fieldService = $fieldService;
        $this->fieldValueService = $fieldValueService;
    }

    /**
     * @param Entity|Item $entity
     * @param string|null $include
     *
     * @return array|null
     */
    public function getApiObject(
        Entity $entity,
        string $include = null,
    ): ?array {
        if(!isset($include)) {
            $include = self::MODEL_INCLUDE;
        }

        $includes = $this->parseIncludesString($include);

        $result = [
			'id' => $entity->getId(),
        ];

        if($this->shouldInclude(self::MODEL_INCLUDE, $includes) || $this->shouldInclude(self::TITLE_INCLUDE, $includes)) {
            $result["title"] = $entity->getTitle();
        }

        if($this->shouldInclude(self::MODEL_INCLUDE, $includes) || $this->shouldInclude(self::FIELDS_ORDER_INCLUDE, $includes)) {
            $result["fieldsOrder"] = $entity->getFieldsOrder();
        }

        if($this->shouldInclude(self::FIELDS_INCLUDE, $includes)) {
            $result["fields"] = $this->getFields($entity);
        }

        return $result;
    }

    /**
     * @param Item $entity
     *
     * @return array
     * @throws DoesNotExistException
     */
	public function getFields(Item $entity) {
        return $this->fieldValueService->findAllInOrder($entity);
	}

}