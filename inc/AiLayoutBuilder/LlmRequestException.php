<?php
/**
 * Thrown by LlmClient on a failed/timed-out/malformed-JSON response from
 * the configured external AI endpoint.
 *
 * @package Shadcn
 * @since 1.0.0
 */

namespace Shadcn\AiLayoutBuilder;

class LlmRequestException extends \RuntimeException {}
