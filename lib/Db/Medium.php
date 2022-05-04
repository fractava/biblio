<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Medium extends Entity implements JsonSerializable {
	protected $title;
	protected $fieldsOrder;
	protected $userId;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'userId' => $this->userId,
			'title' => $this->title,
			'fieldsOrder' => $this->fieldsOrder
		];
	}
}
