<?php

namespace viget\base\models;

/**
 * The outcome of SearchIndex::optimize(): whether a statement ran, and a sentence saying what
 * ran or why nothing did.
 */
final readonly class OptimizeResult
{
    public function __construct(
        public bool $ran,
        public string $message,
    ) {
    }
}
