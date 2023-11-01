<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\LoanService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
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
	public function index(int $collectionId): DataResponse {
		return new DataResponse($this->service->findAll($collectionId));
	}

	/**
	 * Get specific loan
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $barcode, int $customerId, int $until): DataResponse {
		return new DataResponse($this->service->createByItemInstanceBarcode($barcode, $customerId, $until));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(string $barcode, ?int $until): DataResponse {
		return $this->handleNotFound(function () use ($barcode, $until) {
			return $this->service->updateByItemInstanceBarcode($barcode, $until);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(string $barcode): DataResponse {
		return $this->handleNotFound(function () use ($barcode) {
			return $this->service->deleteByItemInstanceBarcode($barcode);
		});
	}
}
