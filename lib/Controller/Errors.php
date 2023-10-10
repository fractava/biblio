<?php

namespace OCA\Biblio\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\Biblio\Errors\NotFoundException;

trait Errors {
	protected function handleNotFound(Closure $callback): DataResponse {
		try {
			return new DataResponse($callback());
		} catch (NotFoundException $e) {
			return $this->notFoundResponse($e);
		}
	}

	private function notFoundResponse(NotFoundException $e) {
		$response = ['error' => get_class($e), 'message' => $e->getMessage()];
		return new DataResponse($response, Http::STATUS_NOT_FOUND);
	}
}
