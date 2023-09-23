<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Medium extends Entity implements JsonSerializable {
	protected $title;
	protected $fieldsOrder;
	protected $collectionId;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'collectionId' => $this->collectionId,
			'title' => $this->title,
			'fieldsOrder' => $this->fieldsOrder
		];
	}
}
