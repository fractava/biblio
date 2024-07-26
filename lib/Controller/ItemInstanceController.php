<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemInstanceService;
use OCA\Biblio\Errors\BarcodeAlreadyExists;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\Biblio\Traits\ApiObjectController;

class ItemInstanceController extends Controller {
	use ApiObjectController;

	/** @var ItemInstanceService */
	private $service;

	use Errors;

	public function __construct(IRequest $request,
		ItemInstanceService $service) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(int $collectionId, ?string $include, ?string $filter, ?string $sort = null, ?bool $sortReverse = null, ?int $limit, ?int $offset): JSONResponse {
		$includes = $this->parseIncludesString($include);
		$filters = $this->parseFilterString($filter);

		[$entities, $meta] = $this->service->findAll($collectionId, $includes, $filters, $sort, $sortReverse, $limit, $offset);

		return new JSONResponse([
			"meta" => $meta,
			"result" => $entities,
		], Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $collectionId, int $instanceId): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $instanceId) {
			$result = $this->service->find($collectionId, $instanceId);
			return new JSONResponse($result, Http::STATUS_OK);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function showByBarcode(int $collectionId, string $barcode): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $barcode) {
			$result = $this->service->findByBarcode($collectionId, $barcode);
			return new JSONResponse($result, Http::STATUS_OK);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, string $barcode, int $itemId): JSONResponse {
		try {
			$result = $this->service->create($collectionId, $barcode, $itemId);
			return new JSONResponse($result, Http::STATUS_OK);
		} catch (BarcodeAlreadyExists $e) {
			return $this->errorResponse($e);
		}
	}

	/**
	 * @NoAdminRequired
	 */
	public function batchCreateTest(int $collectionId, int $itemId, string $prefix, int $startNumber, int $count, string $suffix) {
		$result = $this->service->batchCreateTest($collectionId, $itemId, $prefix, $startNumber, $count, $suffix);
		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 */
	public function batchCreate(int $collectionId, int $itemId, string $prefix, int $startNumber, int $count, string $suffix) {
		$result = $this->service->batchCreate($collectionId, $itemId, $prefix, $startNumber, $count, $suffix);
		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $instanceId): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $instanceId) {
			return $this->service->delete($collectionId, $instanceId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroyByBarcode(int $collectionId, string $barcode): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $barcode) {
			return $this->service->deleteByBarcode($collectionId, $barcode);
		});
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function barcodePrefix(int $collectionId, int $itemId): JSONResponse {
		return new JSONResponse($this->service->getBarcodePrefix($collectionId, $itemId));
	}
}
