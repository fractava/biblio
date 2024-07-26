<?php

namespace OCA\Biblio\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\CustomerFieldValueService;
use OCA\Biblio\Traits\ApiObjectController;

class CustomerFieldValueController extends Controller {
	use ApiObjectController;

	/** @var CustomerFieldValueService */
	private $service;

	use Errors;

	public function __construct(IRequest $request, CustomerFieldValueService $service) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
	}

	/**
	 * Get all customer fields of collection
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(int $collectionId, int $customerId, ?string $include, ?string $filter, ?int $limit, ?int $offset): JSONResponse {
		$includes = $this->parseIncludesString($include);
		$filters = $this->parseFilterString($filter);

		return new JSONResponse($this->service->findAll($collectionId, $customerId, $includes, $filters, $limit, $offset), Http::STATUS_OK);
	}

	/**
	 * Get specific customer field
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $collectionId, int $customerId, int $fieldId, ?string $include): JSONResponse {
		$includes = $this->parseIncludesString($include);

		return $this->handleNotFound(function () use ($collectionId, $customerId, $fieldId, $includes) {
			return $this->service->find([
				"collectionId" => $collectionId,
				"customerId" => $customerId,
				"fieldId" => $fieldId,
			], $includes);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $customerId, int $fieldId, string $value = null): JSONResponse {
		// update value (or create db entry if not set previously)
		$this->service->updateByCustomerAndFieldId($customerId, $fieldId, $value);

		// return API object including field
		return $this->handleNotFound(function () use ($collectionId, $customerId, $fieldId) {
			return $this->service->find([
				"collectionId" => $collectionId,
				"customerId" => $customerId,
				"fieldId" => $fieldId,
			], ["model", "field"]);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $customerId, int $fieldId): JSONResponse {
		return $this->handleNotFound(function () use ($customerId, $fieldId) {
			return $this->service->deleteByCustomerAndFieldId($customerId, $fieldId);
		});
	}
}
