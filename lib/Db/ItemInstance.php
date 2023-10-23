<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class ItemInstance extends Entity implements JsonSerializable {
	protected $barcode;
	protected $itemId;

	public function __construct() {
		$this->addType('itemId','integer');
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'barcode' => $this->barcode,
			'itemId' => $this->itemId,
		];
	}
}
