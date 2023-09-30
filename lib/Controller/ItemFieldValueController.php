<?php

namespace OCA\Biblio\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemFieldValueService;
use OCA\Biblio\Traits\ApiObjectController;

class ItemFieldValueController extends Controller {
	use ApiObjectController;

	/** @var ItemFieldValueService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
                                ItemFieldValueService $service,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * Get all fields of collection
	 * @NoAdminRequired
     * @NoCSRFRequired
	 */
	public function index(int $collectionId, int $itemId, ?string $include, ?string $filter, ?int $limit, ?int $offset): DataResponse {
		return new DataResponse($this->service->findAll($collectionId, $itemId, $include, $filter, $limit, $offset));
	}

	/**
	 * Get specific field
	 * @NoAdminRequired
     * @NoCSRFRequired
	 */
	public function show(int $collectionId, int $itemId, int $fieldId, ?string $include): DataResponse {
		$includes = $this->parseIncludesString($include);

		return $this->handleNotFound(function () use ($collectionId, $itemId, $fieldId, $includes) {
            return DataResponse($this->service->find([
				"collectionId" => $collectionId,
                "itemId" => $itemId,
                "fieldId" => $fieldId,
            ], $includes));
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $itemId, int $fieldId, string $value = null): DataResponse {
		// update value (or create db entry if not set previously)
		return new DataResponse($this->service->updateByItemAndFieldId($itemId, $fieldId, $value));
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
