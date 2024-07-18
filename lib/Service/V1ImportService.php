<?php

namespace OCA\Biblio\Service;

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

	/** @var ItemInstanceService */
	private $itemInstanceService;

	/** @var LoanService */
	private $loanService;

	/** @var LoanFieldService */
	private $loanFieldService;

	/** @var LoanFieldValueService */
	private $loanFieldValueService;

	public function __construct(CollectionService $collectionService,
		CustomerService $customerService,
		CustomerFieldService $customerFieldService,
		CustomerFieldValueService $customerFieldValueService,
		ItemService $itemService,
		ItemFieldService $itemFieldService,
		ItemFieldValueService $itemFieldValueService,
		ItemInstanceService $itemInstanceService,
		LoanService $loanService,
		LoanFieldService $loanFieldService,
		LoanFieldValueService $loanFieldValueService) {
		$this->collectionService = $collectionService;
		$this->customerService = $customerService;
		$this->customerFieldService = $customerFieldService;
		$this->customerFieldValueService = $customerFieldValueService;
		$this->itemService = $itemService;
		$this->itemFieldService = $itemFieldService;
		$this->itemFieldValueService = $itemFieldValueService;
		$this->itemInstanceService = $itemInstanceService;
		$this->loanService = $loanService;
		$this->loanFieldService = $loanFieldService;
		$this->loanFieldValueService = $loanFieldValueService;
	}

	public function import(array $data, string $firstMember) {
		$collection = $this->collectionService->create("V1 Import", "[]", "[]", "[]", $firstMember);
		$collectionId = $collection->getId();

		$importedCustomersTable = $this->getTable($data, "customers");
		$importedClassesTable = $this->getTable($data, "classes");

		$importedItemsTable = $this->getTable($data, "medias");
		$importedSubjectsTable = $this->getTable($data, "subjects");
		$importedItemInstancesTable = $this->getTable($data, "media_instances");

		$newClassesField = $this->convertToSelectField($this->customerFieldService, $collectionId, $importedClassesTable, "Class");

		$customerIdMapping = [];

		foreach ($importedCustomersTable["data"] as $customer) {
			$newCustomer = $this->customerService->create($collectionId, $customer["name"]);
			$newCustomerId = $newCustomer->getId();

			$customerIdMapping[(int) $customer["id"]] = $newCustomerId;

			if (isset($customer["class_id"])) {
				$this->customerFieldValueService->updateByCustomerAndFieldId($newCustomerId, $newClassesField->getId(), json_encode($customer["class_id"]));
			}
		}

		$newSubjectsField = $this->convertToSelectField($this->itemFieldService, $collectionId, $importedSubjectsTable, "Subject");
		$newAuthorField = $this->itemFieldService->create($collectionId, "short", "Author", json_encode(""), true);
		$newPublisherField = $this->itemFieldService->create($collectionId, "short", "Publisher", json_encode(""), true);
		$newPriceField = $this->itemFieldService->create($collectionId, "short", "Price", json_encode(""), true);
		$newIsbnField = $this->itemFieldService->create($collectionId, "short", "ISBN", json_encode(""), true);

		$itemIdMapping = [];

		foreach ($importedItemsTable["data"] as $item) {
			$newItem = $this->itemService->create($collectionId, $item["title"]);
			$newItemId = $newItem->getId();

			$itemIdMapping[(int) $item["id"]] = $newItemId;

			if (isset($item["subject_id"])) {
				$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newSubjectsField->getId(), json_encode($item["subject_id"]));
			}
			if (isset($item["author"])) {
				$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newAuthorField->getId(), json_encode($item["author"]));
			}
			if (isset($item["publisher"])) {
				$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newPublisherField->getId(), json_encode($item["publisher"]));
			}
			if (isset($item["price"])) {
				$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newPriceField->getId(), json_encode($item["price"]));
			}
			if (isset($item["isbn"])) {
				$this->itemFieldValueService->updateByItemAndFieldId($newItemId, $newIsbnField->getId(), json_encode($item["isbn"]));
			}
		}

		$newHolidayField = $this->loanFieldService->create($collectionId, "checkbox", "holiday", json_encode(""), true);

		foreach ($importedItemInstancesTable["data"] as $itemInstance) {
			$mappedItemId = $itemIdMapping[(int) $itemInstance["media_id"]];

			if (isset($mappedItemId)) {
				$mappedLoanedToCustomerId = null;
				if (isset($itemInstance["loaned_to"]) && $itemInstance["loaned_to"] !== "") {
					$mappedLoanedToCustomerId = $customerIdMapping[(int) $itemInstance["loaned_to"]];
				}

				$loanedUntilTime = null;
				if (isset($itemInstance["loaned_until"]) && $itemInstance["loaned_until"] !== "") {
					$datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $itemInstance["loaned_until"] . " 00:00:00");
					$loanedUntilTime = $datetime->getTimestamp();
				}

				$newItemInstance = $this->itemInstanceService->create($collectionId, $itemInstance["barcode"], $mappedItemId);

				if (isset($mappedLoanedToCustomerId)) {
					$newItemInstanceId = $newItemInstance->getId();

					$holiday = false;
					if (isset($itemInstance["holiday"])) {
						$holidayInt = (int) $itemInstance["holiday"];
						$holiday = (bool) $holidayInt;
					}

					$newLoan = $this->loanService->create($collectionId, $newItemInstanceId, $mappedLoanedToCustomerId, $loanedUntilTime, []);

					$this->loanFieldValueService->updateByLoanAndFieldId($newLoan->getId(), $newHolidayField->getId(), json_encode($holiday));
				}
			}
		}

		return $this->getTable($data, "customers");
	}

	private function getTable(array $data, string $name) {
		foreach ($data as $entry) {
			if ($entry["type"] === "table" && $entry["name"] === $name) {
				return $entry;
			}
		}
	}

	private function convertToSelectField($fieldService, int $collectionId, array $table, string $name) {
		$options = [];
		foreach ($table["data"] as $option) {
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
