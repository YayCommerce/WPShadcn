<?php

namespace Shadcn\BlockSettings;

use Shadcn\Traits\SingletonTrait;

class BlockSettingsBootstrap {
	use SingletonTrait;

	public function __construct() {
		require_once __DIR__ . '/ButtonSize/Caller.php';
		require_once __DIR__ . '/HoverSettings/Caller.php';
		require_once __DIR__ . '/LayoutPicker/Caller.php';
	}
}

BlockSettingsBootstrap::get_instance();
