<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class HistoryEntry extends Entity implements JsonSerializable {
	protected $type;
	protected $subEntryOf;
	protected $userId;
	protected $timestamp;
	protected $collectionId;
	protected $collectionMemberId;
	protected $itemId;
	protected $itemFieldId;
	protected $itemFieldValueId;
	protected $loanId;
	protected $loanFieldId;
	protected $loanFieldValueId;
	protected $customerId;
	protected $customerFieldId;
	protected $customerFieldValueId;
	protected $properties;

	public function __construct() {
		$this->addType('subEntryOf', 'integer');
		$this->addType('timestamp', 'integer');
		$this->addType('collectionId', 'integer');
		$this->addType('collectionMemberId', 'integer');
		$this->addType('itemId', 'integer');
		$this->addType('itemFieldId', 'integer');
		$this->addType('itemFieldValueId', 'integer');
		$this->addType('loanId', 'integer');
		$this->addType('loanFieldId', 'integer');
		$this->addType('loanFieldValueId', 'integer');
		$this->addType('customerId', 'integer');
		$this->addType('customerFieldId', 'integer');
		$this->addType('customerFieldValueId', 'integer');
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'type' => $this->type,
			'subEntryOf' => $this->subEntryOf,
			'userId' => $this->userId,
			'timestamp' => $this->timestamp,
			'collectionId' => $this->collectionId,
			'collectionMemberId' => $this->collectionMemberId,
			'itemId' => $this->itemId,
			'itemFieldId' => $this->itemFieldId,
			'itemFieldValueId' => $this->itemFieldValueId,
			'loanId' => $this->loanId,
			'loanFieldId' => $this->loanFieldId,
			'loanFieldValueId' => $this->loanFieldValueId,
			'customerId' => $this->customerId,
			'customerFieldId' => $this->customerFieldId,
			'customerFieldValueId' => $this->customerFieldValueId,
			'properties' => $this->properties,
		];
	}
}
