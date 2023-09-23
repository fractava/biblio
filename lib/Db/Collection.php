<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Collection extends Entity implements JsonSerializable {
	protected $name;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'name' => $this->name
		];
	}
}