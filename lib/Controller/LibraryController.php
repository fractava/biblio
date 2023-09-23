<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\LibraryService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class LibraryController extends Controller {
	/** @var LibraryService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
                                LibraryService $service,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * Get all libraries the current user has access to
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->userId));
	}

	/**
	 * Get specific library
	 * @NoAdminRequired
	 */
	public function show(int $libraryId): DataResponse {
		return $this->handleNotFound(function () use ($libraryId) {
			return $this->service->find($libraryId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $name): DataResponse {
		if(strlen($name) <= 3) {
			return new DataResponse([
				"error" => "Name must be longer than 3 characters"
			], Http::STATUS_BAD_REQUEST);
		}

		return new DataResponse($this->service->create($name, $this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $libraryId, string $name): DataResponse {
		return $this->handleNotFound(function () use ($libraryId, $name) {
			return $this->service->update($libraryId, $name);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $libraryId): DataResponse {
		return $this->handleNotFound(function () use ($libraryId) {
			return $this->service->delete($libraryId);
		});
	}
}
