<?php

namespace OCA\Biblio\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class LibraryMember extends Entity implements JsonSerializable {
	protected $libraryId;
    protected $userId;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'libraryId' => $this->libraryId,
            'userId' => $this->userId
		];
	}
}
