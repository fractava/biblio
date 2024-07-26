<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\CollectionMemberService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class CollectionMemberController extends Controller {
	/** @var CollectionMemberService */
	private $service;

	use Errors;

	public function __construct(IRequest $request, CollectionMemberService $service) {
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
	 * Get specific collection
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
	public function create(int $collectionId, string $userId): JSONResponse {
		return new JSONResponse($this->service->create($collectionId, $userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $id): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $id) {
			return $this->service->update($collectionId, $id);
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
