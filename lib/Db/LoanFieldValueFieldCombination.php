<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class LoanFieldValueFieldCombination extends Entity implements JsonSerializable {
	protected $collectionId;
	protected $name;
	protected $type;
	protected $settings;
	protected $includeInList;
	protected $loanId;
	protected $fieldId;
	protected $value;

	public function __construct() {
		$this->addType('collectionId','integer');
		$this->addType('includeInList','bool');
		$this->addType('loanId','integer');
		$this->addType('fieldId','integer');
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'collectionId' => $this->collectionId,
			'name' => $this->name,
			'type' => $this->type,
			'settings' => $this->settings,
			'includeInList' => $this->includeInList,
			'loanId' => $this->loanId,
			'fieldId' => $this->fieldId,
			'value' => $this->value
		];
	}
}
