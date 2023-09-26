<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class ItemInstance extends Entity implements JsonSerializable {
	protected $barcode;
	protected $itemId;
    protected $loanedTo;
    protected $loanedUntil;

	public function __construct() {
        $this->addType('itemId','integer');
        $this->addType('loanedTo','integer');
        //$this->addType('loanedUntil','integer');
    }

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'barcode' => $this->barcode,
			'itemId' => $this->itemId,
            'loanedTo' => $this->loanedTo,
            'loanedUntil' => $this->loanedUntil,
		];
	}
}
