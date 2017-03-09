<?php

namespace gear\validators;

use gear\Core;
use gear\interfaces\IObject;

class GSessionValidator extends GObjectValidator
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    public $timeLife = 900;

    public function validateTimeLife(IObject $object)
    {
        if ((time() - strtotime($object->timeSession)) > $this->timeLife) {
            throw Core::SessionExpiredException('Session lifetime has expired');
        }
    }

    public function validateToken(IObject $object)
    {
        if ($object->token !== Core::app()->request->getToken()) {
            throw Core::SessionInvalidTokenException('Invalid session token <{token}>', ['token' => Core::app()->request->getToken()]);
        }
    }
}
