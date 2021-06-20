<?php

namespace OCA\Biblio\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'biblio';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
