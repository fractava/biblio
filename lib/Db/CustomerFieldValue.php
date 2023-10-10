<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CustomerFieldValue extends Entity implements JsonSerializable {
	protected $customerId;
	protected $fieldId;
	protected $value;

	public function __construct() {
        $this->addType('customerId','integer');
        $this->addType('fieldId','integer');
    }

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
            'customerId' => $this->itemId,
			'fieldId' => $this->fieldId,
			'value' => $this->value
		];
	}
}
