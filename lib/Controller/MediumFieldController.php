<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\MediumFieldService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class MediumFieldController extends Controller {
	/** @var MediumFieldService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
								MediumFieldService $service,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * Get all fields of medium by medium id
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return new DataResponse($this->service->findAll($id));
	}

	/**
	 * Get specific field
	 * @NoAdminRequired
	 */
	public function findById(int $id, int $mediumId): DataResponse {
		return $this->handleNotFound(function () use ($id, $mediumId) {
			return $this->service->find($id, $mediumId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $mediumId, string $type, string $title, string $value): DataResponse {
		return new DataResponse($this->service->create($mediumId, $type, $title, $value));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, int $mediumId, string $type = null, string $title = null, string $value = null): DataResponse {
		return $this->handleNotFound(function () use ($id, $mediumId, $type, $title, $value) {
			return $this->service->update($id, $mediumId, $type, $title, $value);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id, int $mediumId): DataResponse {
		return $this->handleNotFound(function () use ($id, $mediumId) {
			return $this->service->delete($id, $mediumId);
		});
	}
	
	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function uniqueTitles(): DataResponse {
		return new DataResponse($this->service->findUniqueTitles());
	}
}
