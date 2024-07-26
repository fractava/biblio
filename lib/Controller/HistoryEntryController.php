<?php

namespace OCA\Biblio\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\HistoryEntryService;
use OCA\Biblio\Traits\ApiObjectController;

class HistoryEntryController extends Controller {
	use ApiObjectController;

	/** @var HistoryEntryService */
	private $service;

	use Errors;

	public function __construct(IRequest $request,
		HistoryEntryService $service) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
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
	public function show(int $collectionId, int $id, ?string $include): JSONResponse {
		$includes = $this->parseIncludesString($include);

		return $this->handleNotFound(function () use ($collectionId, $id, $includes) {
			return $this->service->find($collectionId, $id, $includes);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $id): JSONResponse {
		return $this->handleNotFound(function () use ($collectionId, $id) {
			return $this->service->delete($collectionId, $id);
		});
	}
}
