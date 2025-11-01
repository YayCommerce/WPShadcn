<?php

namespace Shadcn\BlockSettings;

use Shadcn\Traits\SingletonTrait;

class BlockSettingsBootstrap {
	use SingletonTrait;

	public function __construct() {
		require_once __DIR__ . '/ButtonSize/Caller.php';
	}
}

BlockSettingsBootstrap::get_instance();
