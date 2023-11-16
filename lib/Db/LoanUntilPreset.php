<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class LoanUntilPreset extends Entity implements JsonSerializable {
	protected $collectionId;
	protected $type;
	protected $name;
	protected $timestamp;

	public function __construct() {
		$this->addType('collectionId','integer');
		$this->addType('timestamp','integer');
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'collectionId' => $this->collectionId,
			'type' => $this->type,
			'name' => $this->name,
			'timestamp' => $this->timestamp,
		];
	}
}
