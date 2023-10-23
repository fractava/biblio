<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Loan extends Entity implements JsonSerializable {
	protected $itemInstanceId;
	protected $customerId;
	protected $until;

	public function __construct() {
		$this->addType('itemInstanceId','integer');
		$this->addType('customerId','integer');
		$this->addType('until','integer');
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'itemInstanceId' => $this->itemInstanceId,
			'customerId' => $this->customerId,
			'until' => $this->until,
		];
	}
}
