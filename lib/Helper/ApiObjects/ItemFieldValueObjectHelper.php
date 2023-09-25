<?php

namespace OCA\Biblio\Helper\ApiObjects;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Service\ItemFieldService;
use OCA\Biblio\Service\ItemFieldValueService;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\IAppContainer;

class ItemFieldValueObjectHelper extends AbstractObjectHelper {
    const MODEL_INCLUDE = 'model';
    const FIELD_INCLUDE = 'field';

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
        ItemFieldValueService $fieldValueService,
        $userId
    ) {
        parent::__construct($container, $userId);

        $this->fieldValueService = $fieldValueService;
    }

    /**
     * @param array $entity
     * @param string|null $include
     *
     * @return array|null
     */
    public function getApiObject($entity, ?string $include): ?array {
        if(!isset($include)) {
            $include = self::MODEL_INCLUDE;
        }
        
        $includes = $this->parseIncludesString($include);

        if($this->shouldInclude(self::FIELD_INCLUDE, $includes)) {
            $query = $this->fieldValueService->findByItemAndFieldIdIncludingFields($entity["itemId"], $entity["fieldId"]);
        } else {
            $query = $this->fieldValueService->findByItemAndFieldId($entity["itemId"], $entity["fieldId"]);
        }

        $result = this->copyValues($query, $includes);

        return $result;
    }

    private function copyValues(array $query, array $includes) {
        $result = [];

        if($this->shouldInclude(self::MODEL_INCLUDE, $includes)) {
            $result["itemId"] = $query["itemId"];
            $result["fieldId"] = $query["fieldId"];
            $result["value"] = $query["value"];
        }

        if($this->shouldInclude(self::FIELD_INCLUDE, $includes)) {
            $result["collectionId"] = $query["collectionId"];
            $result["name"] = $query["name"];
            $result["type"] = $query["type"];
            $result["settings"] = $query["settings"];
            $result["includeInList"] = $query["includeInList"];
        }
    }

    /**
     * @param $entities
     *
     * @return array
     * @throws DoesNotExistException
     */
	public function getApiObjects($entities, ?string $include): ?array {
        if(!isset($include)) {
            $include = self::MODEL_INCLUDE;
        }

        $includes = $this->parseIncludesString($include);

        if($this->shouldInclude(self::FIELD_INCLUDE, $includes)) {
            $query = $this->fieldValueService->findAllIncludingFields($entities["collectionId"], $entities["itemId"]);
        } else {
            $query = $this->fieldValueService->findAll($entities["itemId"]);
        }

        $result = [];

        foreach ($query as $itemFieldValue) {
            $result[] = this->copyValues($itemFieldValue, $includes);
        }

        return $result;
    }
}