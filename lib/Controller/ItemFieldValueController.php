<?php

namespace OCA\Biblio\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemFieldValueService;
use OCA\Biblio\Traits\ApiObjectController;

class ItemFieldValueController extends Controller {
	use ApiObjectController;

	/** @var ItemFieldValueService */
	private $service;

	use Errors;

	public function __construct(IRequest $request, ItemFieldValueService $service) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
	}

	/**
	 * Get all item fields of collection
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(int $collectionId, int $itemId, ?string $include, ?string $filter, ?int $limit, ?int $offset): JSONResponse {
		$includes = $this->parseIncludesString($include);
		$filters = $this->parseFilterString($filter);

		return new JSONResponse($this->service->findAll($collectionId, $itemId, $includes, $filters, $limit, $offset), Http::STATUS_OK);
	}

	/**
	 * Get specific item field
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $collectionId, int $itemId, int $fieldId, ?string $include): DataResponse {
		$includes = $this->parseIncludesString($include);

		return $this->handleNotFound(function () use ($collectionId, $itemId, $fieldId, $includes) {
			return $this->service->find([
				"collectionId" => $collectionId,
				"itemId" => $itemId,
				"fieldId" => $fieldId,
			], $includes);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $itemId, int $fieldId, string $value = null): DataResponse {
		// update value (or create db entry if not set previously)
		$this->service->updateByItemAndFieldId($itemId, $fieldId, $value);

		// return API object including field
		return $this->handleNotFound(function () use ($collectionId, $itemId, $fieldId) {
			return $this->service->find([
				"collectionId" => $collectionId,
				"itemId" => $itemId,
				"fieldId" => $fieldId,
			], ["model", "field"]);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $itemId, int $fieldId): DataResponse {
		return $this->handleNotFound(function () use ($itemId, $fieldId) {
			return $this->service->deleteByItemAndFieldId($itemId, $fieldId);
		});
	}
}
