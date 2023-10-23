<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class ItemField extends Entity implements JsonSerializable {
	protected $collectionId;
	protected $name;
	protected $type;
	protected $settings;

	public function __construct() {
        $this->addType('collectionId','integer');
    }

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
            'collectionId' => $this->collectionId,
			'name' => $this->name,
			'type' => $this->type,
			'settings' => $this->settings,
		];
	}
}
