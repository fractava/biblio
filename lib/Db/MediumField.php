<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class MediumField extends Entity implements JsonSerializable {
	protected $type;
	protected $title;
	protected $value;
	protected $mediumId;

	public function __construct() {
        $this->addType('mediumId','integer');
    }

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
            'mediumId' => $this->mediumId,
			'type' => $this->type,
			'title' => $this->title,
			'value' => $this->value
		];
	}
}
