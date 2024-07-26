<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\LoanUntilPresetService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
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
	public function index(int $collectionId): JSONResponse {
		return new JSONResponse($this->service->findAll($collectionId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $collectionId, int $id): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $id) {
			return $this->service->find($collectionId, $id);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, string $type, string $name, int $timestamp): JSONResponse {
		return new JSONResponse($this->service->create($collectionId, $type, $name, $timestamp));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $id, ?string $type, ?string $name, ?int $timestamp): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $id, $type, $name, $timestamp) {
			return $this->service->update($collectionId, $id, $type, $name, $timestamp);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $id): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $id) {
			return $this->service->delete($collectionId, $id);
		});
	}
}
