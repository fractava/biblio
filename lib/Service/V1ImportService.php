<?php

namespace OCA\Biblio\Service;

use Exception;

use OCA\Biblio\Service\CollectionService;
use OCA\Biblio\Service\CustomerService;
use OCA\Biblio\Service\CustomerFieldService;
use OCA\Biblio\Service\CustomerFieldValueService;
use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Service\ItemFieldService;

class V1ImportService {
	/** @var CollectionService */
	private $collectionService;

	/** @var CustomerService */
	private $customerService;

	/** @var CustomerFieldService */
	private $customerFieldService;

	/** @var CustomerFieldValueService */
	private $customerFieldValueService;

	/** @var ItemService */
	private $itemService;

	/** @var ItemFieldService */
	private $itemFieldService;

	/** @var ItemFieldValueService */
	private $itemFieldValueService;

	public function __construct(CollectionService $collectionService,
								CustomerService $customerService,
								CustomerFieldService $customerFieldService,
								CustomerFieldValueService $customerFieldValueService,
								ItemService $itemService,
								ItemFieldService $itemFieldService,
								ItemFieldValueService $itemFieldValueService) {
		$this->collectionService = $collectionService;
		$this->customerService = $customerService;
		$this->customerFieldService = $customerFieldService;
		$this->customerFieldValueService = $customerFieldValueService;
		$this->itemService = $itemService;
		$this->itemFieldService = $itemFieldService;
		$this->itemFieldValueService = $itemFieldValueService;
	}

	public function import(array $data, string $firstMember) {
		$collection = $this->collectionService->create("V1 Import", "[]", "[]", $firstMember);
		$collectionId = $collection->getId();

		$importedCustomersTable = $this->getTable($data, "customers");
		$importedClassesTable = $this->getTable($data, "classes");

		$importedItemsTable = $this->getTable($data, "medias");
		$importedSubjectsTable = $this->getTable($data, "subjects");

		$newClassesField = $this->convertToSelectField($this->customerFieldService, $collectionId, $importedClassesTable, "Class");

		foreach($importedCustomersTable["data"] as $customer) {
			$newCustomer = $this->customerService->create($collectionId, $customer["name"]);
			$this->customerFieldValueService->updateByCustomerAndFieldId($newCustomer->getId(), $newClassesField->getId(), json_encode($customer["class_id"]));
		}

		$newSubjectsField = $this->convertToSelectField($this->itemFieldService, $collectionId, $importedSubjectsTable, "Subject");
		$newAuthorField = $this->itemFieldService->create($collectionId, "short", "Author", json_encode(""), true);
		$newPublisherField = $this->itemFieldService->create($collectionId, "short", "Publisher", json_encode(""), true);
		$newPriceField = $this->itemFieldService->create($collectionId, "short", "Price", json_encode(""), true);
		$newIsbnField = $this->itemFieldService->create($collectionId, "short", "ISBN", json_encode(""), true);
		

		foreach($importedItemsTable["data"] as $item) {
			$newItem = $this->itemService->create($collectionId, $item["title"]);
			$newItemId = $newItem->getId();
			$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newSubjectsField->getId(), json_encode($item["subject_id"]));
			$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newAuthorField->getId(), json_encode($item["author"]));
			$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newPublisherField->getId(), json_encode($item["publisher"]));
			$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newPriceField->getId(), json_encode($item["price"]));
			$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newIsbnField->getId(), json_encode($item["isbn"]));
		}

		return $this->getTable($data, "customers");
	}

	private function getTable(array $data, string $name) {
		foreach ($data as $entry) {
			if($entry["type"] === "table" && $entry["name"] === $name) {
				return $entry;
			}
		}
	}

	private function convertToSelectField($fieldService, int $collectionId, array $table, string $name) {
		$options = [];
		foreach($table["data"] as $option) {
			$options[] = [
				"id" => $option["id"],
				"label" => $option["name"],
			];
		}

		return $fieldService->create($collectionId, "select", $name, json_encode([
			"options" => $options,
		]), true);
	}
}