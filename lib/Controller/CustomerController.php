<?php

namespace OCA\Biblio\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\CustomerService;
use OCA\Biblio\Traits\ApiObjectController;

class CustomerController extends Controller {
	use ApiObjectController;

	/** @var CustomerService */
	private $service;


	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request, CustomerService $service, $userId) {
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

		[$entities, $meta] = $this->service->findAll($collectionId, $includes, $filters, $sort, $sortReverse, $limit, $offset);

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
	public function create(int $collectionId, string $name): JSONResponse {
		if (strlen($name) < 3) {
			return new JSONResponse([
				"error" => "Name must be longer than 3 characters"
			], Http::STATUS_BAD_REQUEST);
		}

		$newCustomer = $this->service->create($collectionId, $name);
		$result = $this->service->getApiObjectFromEntity($collectionId, $newCustomer, true, true);

		return new JSONResponse($result, Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $id, string $name = null): DataResponse {
		return $this->handleNotFound(function () use ($collectionId, $id, $name) {
			return $this->service->update($collectionId, $id, $name);
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
