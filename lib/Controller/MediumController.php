<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\MediumService;
use OCA\Biblio\Helper\ApiObjects\MediumObjectHelper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class MediumController extends Controller {
	/** @var MediumService */
	private $service;

	/** @var MediumObjectHelper */
	private $objectHelper;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
								MediumService $service,
								MediumObjectHelper $objectHelper,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->objectHelper = $objectHelper;
		$this->userId = $userId;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(int $libraryId, ?string $include): JSONResponse {
		$entities = $this->service->findAll($libraryId);
		$result = $this->objectHelper->getApiObjects($entities, $include);

		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $libraryId, int $id, ?string $include): DataResponse {
		return $this->handleNotFound(function () use ($id, $libraryId, $include) {
			$medium = $this->service->find($id, $libraryId);
			return $this->objectHelper->getApiObject($medium, $include);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $libraryId, string $title, string $fields): JSONResponse {
		if(strlen($title) < 3) {
			return new JSONResponse([
				"error" => "Title must be longer than 3 characters"
			], Http::STATUS_BAD_REQUEST);
		}

		$fieldsParsed = json_decode($fields, true);
		$newMedium = $this->service->create($title, $fieldsParsed, $libraryId);
		$result = $this->objectHelper->getApiObject($newMedium, "model+fields+fieldsOrder");

		return new JSONResponse($result, Http::STATUS_OK);

		//return new DataResponse($this->service->create($title, $fieldsParsed, $this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $libraryId, int $id, string $title = null,
						   string $fieldsOrder = null): DataResponse {
		return $this->handleNotFound(function () use ($id, $libraryId, $title, $fieldsOrder) {
			return $this->service->update($id, $libraryId, $title, $fieldsOrder);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $libraryId, int $id): DataResponse {
		return $this->handleNotFound(function () use ($id, $libraryId) {
			return $this->service->delete($id, $libraryId);
		});
	}
}
