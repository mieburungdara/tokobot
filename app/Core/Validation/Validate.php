<?php

namespace TokoBot\Core\Validation;

use Attribute;

/**
 * An attribute to automatically validate request data before a controller method is executed.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Validate
{
    /**
     * @param string $formRequestClass The class that defines the validation rules.
     */
    public function __construct(
        public string $formRequestClass
    ) {}
}
