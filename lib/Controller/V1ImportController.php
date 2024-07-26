<?php

namespace OCA\Biblio\Controller;

use OCA\Biblio\AppInfo\Application;
use OCA\Biblio\Service\V1ImportService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class V1ImportController extends Controller {
	/** @var V1ImportService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct(IRequest $request,
		V1ImportService $service,
		$userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function import(array $data): JSONResponse {
		return new JSONResponse($this->service->import($data, $this->userId));
	}
}
