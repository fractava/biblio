<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\MediumService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class MediumController extends Controller {
	/** @var MediumService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
								MediumService $service,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * @NoAdminRequired
	 */
	public function findAllByMediumId(int $medium_id): DataResponse {
		return new DataResponse($this->service->findAll($medium_id));
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
		return new DataResponse($this->service->create($medium_id, $title, $fields));
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
