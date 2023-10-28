<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Collection extends Entity implements JsonSerializable {
	protected $name;
	protected $nomenclatureItem;
	protected $nomenclatureInstance;
	protected $nomenclatureCustomer;
	protected $itemFieldsOrder;
	protected $loanFieldsOrder;
	protected $customerFieldsOrder;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'nomenclatureItem' => $this->nomenclatureItem,
			'nomenclatureInstance' => $this->nomenclatureInstance,
			'nomenclatureCustomer' => $this->nomenclatureCustomer,
			'itemFieldsOrder' => $this->itemFieldsOrder,
			'loanFieldsOrder' => $this->loanFieldsOrder,
			'customerFieldsOrder' => $this->customerFieldsOrder,
		];
	}
}
