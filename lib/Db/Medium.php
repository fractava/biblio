<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Medium extends Entity implements JsonSerializable {
	protected $title;
	protected $fields;
	protected $userId;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'fields' => $this->fields
		];
	}
}
