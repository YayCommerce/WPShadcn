<?php
/**
 * Thrown by PlanResolver when asked to resolve a pattern slug that isn't
 * registered — the resolver's internal whitelist guard (defense in depth
 * alongside Phase 2's REST-layer whitelist filter).
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class UnknownPatternSlugException extends \RuntimeException {}
