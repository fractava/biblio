<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\CollectionService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class CollectionController extends Controller {
	/** @var CollectionService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
		CollectionService $service,
		$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * Get all collections the current user has access to
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->userId));
	}

	/**
	 * Get specific collection
	 * @NoAdminRequired
	 */
	public function show(int $collectionId): DataResponse {
		return $this->handleNotFound(function () use ($collectionId) {
			return $this->service->find($collectionId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $name, string $itemFieldsOrder = "[]", string $customerFieldsOrder = "[]"): DataResponse {
		if (strlen($name) <= 3) {
			return new DataResponse([
				"error" => "Name must be longer than 3 characters"
			], Http::STATUS_BAD_REQUEST);
		}

		return new DataResponse($this->service->create($name, $itemFieldsOrder, $customerFieldsOrder, $this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, ?string $name, ?string $itemFieldsOrder, ?string $customerFieldsOrder): DataResponse {
		return $this->handleNotFound(function () use ($collectionId, $name, $itemFieldsOrder, $customerFieldsOrder) {
			return $this->service->update($collectionId, $name, $itemFieldsOrder, $customerFieldsOrder);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId): DataResponse {
		return $this->handleNotFound(function () use ($collectionId) {
			return $this->service->delete($collectionId);
		});
	}
}
