<?php

namespace OCA\Biblio\Errors;

class BarcodeAlreadyExists extends \Exception {
	public function __construct($barcode) {
		$message = "Barcode " . $barcode . " already exists in collection";
        parent::__construct($message);
	}
}
