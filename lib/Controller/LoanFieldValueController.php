<?php

namespace OCA\Biblio\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\LoanFieldValueService;
use OCA\Biblio\Traits\ApiObjectController;

class LoanFieldValueController extends Controller {
	use ApiObjectController;

	/** @var LoanFieldValueService */
	private $service;

	use Errors;

	public function __construct(IRequest $request, LoanFieldValueService $service) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
	}

	/**
	 * Get all loan fields of collection
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(int $collectionId, int $loanId, ?string $include, ?string $filter, ?int $limit, ?int $offset): JSONResponse {
		$includes = $this->parseIncludesString($include);
		$filters = $this->parseFilterString($filter);

		return new JSONResponse($this->service->findAll($collectionId, $loanId, $includes, $filters, $limit, $offset), Http::STATUS_OK);
	}

	/**
	 * Get specific loan field
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(int $collectionId, int $loanId, int $fieldId, ?string $include): DataResponse {
		$includes = $this->parseIncludesString($include);

		return $this->handleNotFound(function () use ($collectionId, $loanId, $fieldId, $includes) {
			return $this->service->find([
				"collectionId" => $collectionId,
				"loanId" => $loanId,
				"fieldId" => $fieldId,
			], $includes);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $collectionId, int $loanId, int $fieldId, string $value = null): DataResponse {
		// update value (or create db entry if not set previously)
		$this->service->updateByLoanAndFieldId($loanId, $fieldId, $value);

		// return API object including field
		return $this->handleNotFound(function () use ($collectionId, $loanId, $fieldId) {
			return $this->service->find([
				"collectionId" => $collectionId,
				"loanId" => $loanId,
				"fieldId" => $fieldId,
			], ["model", "field"]);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $collectionId, int $loanId, int $fieldId): DataResponse {
		return $this->handleNotFound(function () use ($loanId, $fieldId) {
			return $this->service->deleteByLoanAndFieldId($loanId, $fieldId);
		});
	}
}
