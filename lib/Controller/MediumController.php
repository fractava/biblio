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
	public function index(?string $include): JSONResponse {
		$entities = $this->service->findAll($this->userId);
		$result = $this->objectHelper->getApiObjects($entities, $include);

		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $id, ?string $include): DataResponse {
		return $this->handleNotFound(function () use ($id, $include) {
			$medium = $this->service->find($id, $this->userId);
			return $this->objectHelper->getApiObject($medium, $include);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $title, string $fields): JSONResponse {
		$fieldsParsed = json_decode($fields, true);
		$newMedium = $this->service->create($title, $fieldsParsed, $this->userId);
		$result = $this->objectHelper->getApiObject($newMedium, "model+fields+fieldsOrder");

		return new JSONResponse($result, Http::STATUS_OK);

		//return new DataResponse($this->service->create($title, $fieldsParsed, $this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title = null,
						   string $fieldsOrder = null): DataResponse {
		return $this->handleNotFound(function () use ($id, $title, $fieldsOrder) {
			return $this->service->update($id, $title, $fieldsOrder, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}
