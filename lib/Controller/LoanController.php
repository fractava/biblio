<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\LoanService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class LoanController extends Controller {
	/** @var LoanService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
		LoanService $service,
		$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * Get all loans of collection
	 * @NoAdminRequired
	 */
	public function index(int $collectionId): JSONResponse {
		return new JSONResponse($this->service->findAll($collectionId));
	}

	/**
	 * Get specific loan
	 * @NoAdminRequired
	 */
	public function show(int $collectionId, string $barcode): JSONResponse {
		return $this->handleNotFound(function () use ($barcode) {
			return $this->service->findByItemInstanceBarcode($barcode);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, string $barcode, int $customerId, int $until, array $fieldValues): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $barcode, $customerId, $until, $fieldValues) {
			return $this->service->createByItemInstanceBarcode($collectionId, $barcode, $customerId, $until, $fieldValues);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, string $barcode, ?int $until): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $barcode, $until) {
			return $this->service->updateByItemInstanceBarcode($collectionId, $barcode, $until);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, string $barcode): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $barcode) {
			return $this->service->deleteByItemInstanceBarcode($collectionId, $barcode);
		});
	}
}
