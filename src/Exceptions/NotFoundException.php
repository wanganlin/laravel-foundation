<?php

declare(strict_types=1);

namespace Juling\Foundation\Exceptions;

use Juling\Foundation\Contracts\EnumMethodInterface;
use Juling\Foundation\Enums\BusinessEnum;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends BusinessException
{
    public function __construct(EnumMethodInterface|string|null $e = null, $code = Response::HTTP_INTERNAL_SERVER_ERROR, $previous = null)
    {
        if (is_null($e)) {
            $enum = BusinessEnum::NOT_FOUND;
            $e = $enum->getDescription();
            $code = $enum->getValue();
        }

        parent::__construct($e, $code, $previous);
    }
}
