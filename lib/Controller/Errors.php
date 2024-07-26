<?php

namespace OCA\Biblio\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;

use OCA\Biblio\Errors\NotFoundException;

trait Errors {
	private function errorResponse(\Exception $e, $status = Http::STATUS_BAD_REQUEST): JSONResponse {
		$response = ['error' => get_class($e), 'message' => $e->getMessage()];
		return new JSONResponse($response, $status);
	}

	protected function handleNotFound(Closure $callback): JSONResponse {
		try {
			return new JSONResponse($callback());
		} catch (NotFoundException $e) {
			return $this->errorResponse($e, Http::STATUS_NOT_FOUND);
		}
	}
}
