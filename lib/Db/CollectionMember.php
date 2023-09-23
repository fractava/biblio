<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class CollectionMember extends Entity implements JsonSerializable {
	protected $collectionId;
    protected $userId;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'collectionId' => $this->collectionId,
            'userId' => $this->userId
		];
	}
}
