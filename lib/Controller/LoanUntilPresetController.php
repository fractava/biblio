<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\LoanUntilPresetService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class LoanUntilPresetController extends Controller {
	/** @var LoanUntilPresetService */
	private $service;

	use Errors;

	public function __construct(IRequest $request, LoanUntilPresetService $service) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(int $collectionId): DataResponse {
		return new DataResponse($this->service->findAll($collectionId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $collectionId, int $id): DataResponse {
		return $this->handleNotFound(function () use ($collectionId, $id) {
			return $this->service->find($collectionId, $id);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, string $type, string $name, int $timestamp): DataResponse {
		return new DataResponse($this->service->create($collectionId, $type, $name, $timestamp));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $id, ?string $type, ?string $name, ?int $timestamp): DataResponse {
		return $this->handleNotFound(function () use ($collectionId, $id, $type, $name, $timestamp) {
			return $this->service->update($collectionId, $id, $type, $name, $timestamp);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $id): DataResponse {
		return $this->handleNotFound(function () use ($collectionId, $id) {
			return $this->service->delete($collectionId, $id);
		});
	}
}
