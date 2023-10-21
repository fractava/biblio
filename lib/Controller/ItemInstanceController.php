<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemInstanceService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
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

		list($entities, $meta) = $this->service->findAll($collectionId, $includes, $filters, $sort, $sortReverse, $limit, $offset);

		return new JSONResponse([
			"meta" => $meta,
			"result" => $entities,
		], Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 */
	public function getAllOfItem(int $collectionId, int $itemId): JSONResponse {
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
	public function create(int $collectionId, int $itemId, string $barcode): JSONResponse {
		$result = $this->service->create($barcode, $itemId);
		return new JSONResponse($result, Http::STATUS_OK);
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
