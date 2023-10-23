<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Item extends Entity implements JsonSerializable {
	protected $collectionId;
	protected $title;

	public function __construct() {
		$this->addType('collectionId','integer');
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'collectionId' => $this->collectionId,
			'title' => $this->title,
		];
	}
}
