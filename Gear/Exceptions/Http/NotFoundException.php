<?php

namespace Gear\Exceptions\Http;

use Gear\Interfaces\Http\HttpExceptionInterface;
use Gear\Interfaces\Http\HttpInterface;
use Gear\Library\GearException;

class NotFoundException extends GearException implements HttpExceptionInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected string $defaultMessage = 'HTTP/1.1 ' . HttpInterface::HTTP_STATUS_NOT_FOUND;
    /* Public */
}
