<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemInstanceService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ItemInstanceController extends Controller {
	/** @var ItemInstanceService */
	private $service;

	use Errors;

	public function __construct(IRequest $request,
                                ItemInstanceService $service) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->objectHelper = $objectHelper;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(int $collectionId, int $itemId): JSONResponse {
		$result = $this->service->findAll($itemId);

		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $collectionId, int $itemId, int $instanceId): JSONResponse {
		return $this->handleNotFound(function () use ($instanceId) {
			$result = $this->service->find($instanceId);
            return new JSONResponse($result, Http::STATUS_OK);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function showByBarcode(int $collectionId, int $itemId, string $barcode): JSONResponse {
		return $this->handleNotFound(function () use ($barcode) {
			$result = $this->service->findByBarcode($barcode);
            return new JSONResponse($result, Http::STATUS_OK);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, int $itemId, string $barcode, ?int $loanedTo, ?int $loanedUntil): JSONResponse {
		$result = $this->service->create($barcode, $itemId, $loanedTo, $loanedUntil);
		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $itemId, int $instanceId, ?string $barcode, ?int $loanedTo, ?int $loanedUntil): DataResponse {
		return $this->handleNotFound(function () use ($collectionId, $itemId, $instanceId, $barcode, $loanedTo, $loanedUntil) {
			return $this->service->update($instanceId, $barcode, $loanedTo, $loanedUntil);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function updateByBarcode(int $collectionId, int $itemId, string $barcode, ?int $loanedTo, ?int $loanedUntil): DataResponse {
		// this endpoint does not allow the modification of the barcode, since it is used as the reference
		return $this->handleNotFound(function () use ($collectionId, $itemId, $barcode, $loanedTo, $loanedUntil) {
			return $this->service->updateByBarcode($barcode, $loanedTo, $loanedUntil);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $itemId, int $instanceId): DataResponse {
		return $this->handleNotFound(function () use ($instanceId) {
			return $this->service->delete($instanceId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroyByBarcode(int $collectionId, int $itemId, string $barcode): DataResponse {
		return $this->handleNotFound(function () use ($barcode) {
			return $this->service->deleteByBarcode($barcode);
		});
	}
}
