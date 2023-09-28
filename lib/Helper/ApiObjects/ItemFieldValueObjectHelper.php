<?php

namespace OCA\Biblio\Helper\ApiObjects;

use OCA\Biblio\Db\Item;
use OCA\Biblio\Db\ItemFieldValue;
use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Service\ItemFieldService;
use OCA\Biblio\Service\ItemFieldValueService;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\IAppContainer;

class ItemFieldValueObjectHelper extends AbstractObjectHelper {
    const MODEL_INCLUDE = 'model';
    const FIELD_INCLUDE = 'field';
    const FIELD_INCLUDED_IN_LIST_INCLUDE = 'field_included_in_list';
    

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

        $includeField = false;

        if($this->shouldInclude(self::FIELD_INCLUDE, $includes)) {
            $query = $this->fieldValueService->findByItemAndFieldIdIncludingField($entity["collectionId"], $entity["itemId"], $entity["fieldId"]);
            $includeField = true;
        } else {
            $query = $this->fieldValueService->findByItemAndFieldId($entity["itemId"], $entity["fieldId"]);
        }

        $result = $this->copyValues($query, $this->shouldInclude(self::MODEL_INCLUDE, $includes), $includeField);

        return $result;
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

        $includeField = false;

        if($this->shouldInclude(self::FIELD_INCLUDE, $includes)) {
            $query = $this->fieldValueService->findAllIncludingFields($entities["collectionId"], $entities["itemId"], false);
            $includeField = true;
        } else if($this->shouldInclude(self::FIELD_INCLUDED_IN_LIST_INCLUDE, $includes)) {
            $query = $this->fieldValueService->findAllIncludingFields($entities["collectionId"], $entities["itemId"], true);
            $includeField = true;
        } else {
            $query = $this->fieldValueService->findAll($entities["itemId"]);
        }

        $result = [];

        foreach ($query as $itemFieldValue) {
            $result[] = $this->copyValues($itemFieldValue, $this->shouldInclude(self::MODEL_INCLUDE, $includes), $includeField);
        }

        return $result;
    }

    //private function entityToArray($entity) {
    //    return $entity->jsonSerialize();
    //}

    private function copyValues($entity, bool $includeModel, bool $includeField) {
        $result = [];

        if($includeModel) {
            $result["itemId"] = $entity->getItemId();
            $result["fieldId"] = $entity->getFieldId();
            $result["value"] = $entity->getValue();
        }

        if($includeField) {
            $result["collectionId"] = $entity->getCollectionId();
            $result["name"] = $entity->getName();
            $result["type"] = $entity->getType();
            $result["settings"] = $entity->getSettings();
            $result["includeInList"] = $entity->getIncludeInList();
        }

        return $result;
    }
}