<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class ItemFieldValue extends Entity implements JsonSerializable {
	protected $itemId;
	protected $fieldId;
	protected $value;

	public function __construct() {
        $this->addType('itemId','integer');
        $this->addType('fieldId','integer');
    }

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
            'itemId' => $this->itemId,
			'fieldId' => $this->fieldId,
			'value' => $this->value
		];
	}
}
