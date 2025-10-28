<?php

namespace Shadcn;

use Shadcn\Traits\SingletonTrait;

class Integrations {
	use SingletonTrait;

	protected function __construct() {
		require_once __DIR__ . '/Integrations/WooCommerce.php';
	}
}

Integrations::get_instance();
