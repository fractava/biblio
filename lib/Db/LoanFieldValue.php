<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class LoanFieldValue extends Entity implements JsonSerializable {
	protected $loanId;
	protected $fieldId;
	protected $value;

	public function __construct() {
        $this->addType('loanId','integer');
        $this->addType('fieldId','integer');
    }

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
            'loanId' => $this->loanId,
			'fieldId' => $this->fieldId,
			'value' => $this->value
		];
	}
}
