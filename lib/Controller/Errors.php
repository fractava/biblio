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
			$this->notFoundResponse($e);
		}
	}

	private function notFoundResponse(\Exception $e) {
		$message = ['message' => $e->getMessage()];
		return new DataResponse($message, Http::STATUS_NOT_FOUND);
	}
}
