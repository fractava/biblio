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
	 * Get by Media Id
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return new DataResponse($this->service->findAll($id));
	}

	/**
	 * @NoAdminRequired
	 */
	public function findById(int $id, int $medium_id): DataResponse {
		return $this->handleNotFound(function () use ($id, $medium_id) {
			return $this->service->find($id, $medium_id);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $medium_id, string $title, string $value): DataResponse {
		return new DataResponse($this->service->create($medium_id, $title, $value));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, int $medium_id, string $title = null, string $value = null): DataResponse {
		return $this->handleNotFound(function () use ($id, $medium_id, $title, $value) {
			return $this->service->update($id, $medium_id, $title, $value);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id, int $medium_id): DataResponse {
		return $this->handleNotFound(function () use ($id, $medium_id) {
			return $this->service->delete($id, $medium_id);
		});
	}
}
