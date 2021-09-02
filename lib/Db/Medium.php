<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Medium extends Entity implements JsonSerializable {
	protected $title;
	protected $fields;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'fields_order' => $this->fields
		];
	}
}
