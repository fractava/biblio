<?php

namespace OCA\Biblio\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemFieldService;

class ItemFieldController extends Controller {
	/** @var ItemFieldService */
	private $service;

	use Errors;

	public function __construct(IRequest $request, ItemFieldService $service) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
	}

	/**
	 * Get all item fields of collection
	 * @NoAdminRequired
	 */
	public function index(int $collectionId): DataResponse {
		return new DataResponse($this->service->findAll($collectionId));
	}

	/**
	 * Get specific item field
	 * @NoAdminRequired
	 */
	public function show(int $collectionId, int $id): DataResponse {
		return $this->handleNotFound(function () use ($id, $collectionId) {
			return $this->service->find($id, $collectionId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, string $type, string $name, string $settings, bool $includeInList): DataResponse {
		return new DataResponse($this->service->create($collectionId, $type, $name, $settings, $includeInList));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $id, string $type = null, string $name = null, string $settings = null, bool $includeInList = null): DataResponse {
		return $this->handleNotFound(function () use ($id, $collectionId, $type, $name, $settings, $includeInList) {
			return $this->service->update($id, $collectionId, $type, $name, $settings, $includeInList);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $id): DataResponse {
		return $this->handleNotFound(function () use ($id, $collectionId) {
			return $this->service->delete($id, $collectionId);
		});
	}
}
