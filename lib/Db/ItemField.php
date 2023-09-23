<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class ItemField extends Entity implements JsonSerializable {
	protected $type;
	protected $title;
	protected $value;
	protected $itemId;

	public function __construct() {
        $this->addType('itemId','integer');
    }

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
            'itemId' => $this->itemId,
			'type' => $this->type,
			'title' => $this->title,
			'value' => $this->value
		];
	}
}
