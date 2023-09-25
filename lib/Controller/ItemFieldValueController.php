<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemFieldValueService;
use OCA\Biblio\Helper\ApiObjects\ItemFieldValueObjectHelper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ItemFieldValueController extends Controller {
	/** @var ItemFieldValueService */
	private $service;

    /** @var ItemFieldValueObjectHelper */
	private $objectHelper;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
                                ItemFieldValueService $service,
                                ItemFieldValueObjectHelper $objectHelper,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
        $this->objectHelper = $objectHelper;
		$this->userId = $userId;
	}

	/**
	 * Get all fields of collection
	 * @NoAdminRequired
     * @NoCSRFRequired
	 */
	public function index(int $collectionId, int $itemId): DataResponse {
		return new DataResponse($this->objectHelper->getApiObjects([
            "itemId" => $itemId,
        ], $include));
	}

	/**
	 * Get specific field
	 * @NoAdminRequired
     * @NoCSRFRequired
	 */
	public function show(int $collectionId, int $itemId, int $fieldId, ?string $include): DataResponse {
		return $this->handleNotFound(function () use ($itemId, $fieldId) {
            return $this->objectHelper->getApiObject([
                "itemId" => $itemId,
                "fieldId" => $fieldId,
            ], $include);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, int $itemId, int $fieldId, string $value): DataResponse {
		return new DataResponse($this->service->create($itemId, $fieldId, $value));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $itemId, int $fieldId, string $value = null): DataResponse {
		return $this->handleNotFound(function () use ($itemId, $fieldId, $value) {
			return $this->service->updateByItemAndFieldId($itemId, $fieldId, $value);
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
