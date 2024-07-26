<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\CollectionService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
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
	public function index(): JSONResponse {
		return new JSONResponse($this->service->findAll($this->userId));
	}

	/**
	 * Get specific collection
	 * @NoAdminRequired
	 */
	public function show(int $collectionId): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId) {
			return $this->service->find($collectionId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $name, string $itemFieldsOrder = "[]", string $loanFieldsOrder = "[]", string $customerFieldsOrder = "[]"): JSONResponse {
		if (strlen($name) <= 3) {
			return new JSONResponse([
				"error" => "Name must be longer than 3 characters"
			], Http::STATUS_BAD_REQUEST);
		}

		return new JSONResponse($this->service->create($name, $itemFieldsOrder, $loanFieldsOrder, $customerFieldsOrder, $this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(
		int $collectionId,
		?string $name,
		?string $nomenclatureItem,
		?string $nomenclatureInstance,
		?string $nomenclatureCustomer,
		?string $itemFieldsOrder,
		?string $loanFieldsOrder,
		?string $customerFieldsOrder): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $name, $nomenclatureItem, $nomenclatureInstance, $nomenclatureCustomer, $itemFieldsOrder, $loanFieldsOrder, $customerFieldsOrder) {
			return $this->service->update($collectionId, $name, $nomenclatureItem, $nomenclatureInstance, $nomenclatureCustomer, $itemFieldsOrder, $loanFieldsOrder, $customerFieldsOrder);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId) {
			return $this->service->delete($collectionId);
		});
	}
}
