<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemFieldService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ItemFieldController extends Controller {
	/** @var ItemFieldService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
								ItemFieldService $service,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * Get all fields of item by item id
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return new DataResponse($this->service->findAll($id));
	}

	/**
	 * Get specific field
	 * @NoAdminRequired
	 */
	public function findById(int $id, int $itemId): DataResponse {
		return $this->handleNotFound(function () use ($id, $itemId) {
			return $this->service->find($id, $itemId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $itemId, string $type, string $title, string $value): DataResponse {
		return new DataResponse($this->service->create($itemId, $type, $title, $value));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, int $itemId, string $type = null, string $title = null, string $value = null): DataResponse {
		return $this->handleNotFound(function () use ($id, $itemId, $type, $title, $value) {
			return $this->service->update($id, $itemId, $type, $title, $value);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id, int $itemId): DataResponse {
		return $this->handleNotFound(function () use ($id, $itemId) {
			return $this->service->delete($id, $itemId);
		});
	}
	
	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function uniqueTitles(): DataResponse {
		return new DataResponse($this->service->findUniqueTitles());
	}
}
