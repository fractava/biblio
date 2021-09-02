<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class MediumField extends Entity implements JsonSerializable {
	protected $title;
	protected $value;
	protected $mediumId;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
            'medium_id' => $this->mediumId,
			'title' => $this->title,
			'value' => $this->value
		];
	}
}
