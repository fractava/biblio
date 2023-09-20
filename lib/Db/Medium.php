<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Medium extends Entity implements JsonSerializable {
	protected $title;
	protected $fieldsOrder;
	protected $libraryId;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'libraryId' => $this->libraryId,
			'title' => $this->title,
			'fieldsOrder' => $this->fieldsOrder
		];
	}
}
