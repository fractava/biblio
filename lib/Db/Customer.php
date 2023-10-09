<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Customer extends Entity implements JsonSerializable {
	protected $collectionId;
	protected $name;

	public function __construct() {
        $this->addType('collectionId','integer');
    }

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'collectionId' => $this->collectionId,
			'name' => $this->name,
		];
	}
}
