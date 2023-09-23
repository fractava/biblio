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
	public function index(int $collectionId, ?string $include): JSONResponse {
		$entities = $this->service->findAll($collectionId);
		$result = $this->objectHelper->getApiObjects($entities, $include);

		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $collectionId, int $id, ?string $include): DataResponse {
		return $this->handleNotFound(function () use ($id, $collectionId, $include) {
			$medium = $this->service->find($id, $collectionId);
			return $this->objectHelper->getApiObject($medium, $include);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, string $title, string $fields): JSONResponse {
		if(strlen($title) < 3) {
			return new JSONResponse([
				"error" => "Title must be longer than 3 characters"
			], Http::STATUS_BAD_REQUEST);
		}

		$fieldsParsed = json_decode($fields, true);
		$newMedium = $this->service->create($title, $fieldsParsed, $collectionId);
		$result = $this->objectHelper->getApiObject($newMedium, "model+fields+fieldsOrder");

		return new JSONResponse($result, Http::STATUS_OK);

		//return new DataResponse($this->service->create($title, $fieldsParsed, $this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $id, string $title = null,
						   string $fieldsOrder = null): DataResponse {
		return $this->handleNotFound(function () use ($id, $collectionId, $title, $fieldsOrder) {
			return $this->service->update($id, $collectionId, $title, $fieldsOrder);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $id): DataResponse {
		return $this->handleNotFound(function () use ($id, $collectionId) {
			return $this->service->delete($id, $collectionId);
		});
	}
}
