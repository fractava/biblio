<?php

namespace OCA\Biblio\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\ItemService;
use OCA\Biblio\Traits\ApiObjectController;

class ItemController extends Controller {
	use ApiObjectController;

	/** @var ItemService */
	private $service;


	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
								ItemService $service,
								$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(int $collectionId, ?string $include, ?string $filter, ?string $sort = null, ?bool $sortReverse = null, ?int $limit, ?int $offset): JSONResponse {
		$includes = $this->parseIncludesString($include);
		$filters = $this->parseFilterString($filter);

		list($entities, $meta) = $this->service->findAll($collectionId, $includes, $filters, $sort, $sortReverse, $limit, $offset);

		return new JSONResponse([
			"meta" => $meta,
			"result" => $entities,
		], Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $collectionId, int $id, ?string $include): DataResponse {
		$includes = $this->parseIncludesString($include);

		return $this->handleNotFound(function () use ($collectionId, $id, $includes) {
			return $this->service->find($collectionId, $id, $includes);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $collectionId, string $title): JSONResponse {
		if(strlen($title) < 3) {
			return new JSONResponse([
				"error" => "Title must be longer than 3 characters"
			], Http::STATUS_BAD_REQUEST);
		}

		$newItem = $this->service->create($collectionId, $title);
		$result = $this->service->getApiObjectFromEntity($collectionId, $newItem, true, true);

		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $id, string $title = null): DataResponse {
		return $this->handleNotFound(function () use ($collectionId, $id, $title) {
			return $this->service->update($collectionId, $id, $title);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $id): DataResponse {
		return $this->handleNotFound(function () use ($collectionId, $id) {
			return $this->service->delete($collectionId, $id);
		});
	}
}
