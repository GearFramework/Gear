<?php

namespace gear\validators;

use gear\Core;
use gear\interfaces\IObject;

class GSessionValidator
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    public function __invoke(IObject $object, $name, $value, $default = null)
    {
        return $this->validate($object, $name, $value, $default);
    }

    public function validateTimeLife(IObject $object)
    {
        if ((time() - $object->timeSession) > $object->timeLife) {
            throw self::exceptionSessionExpired('Session lifetime has expired');
        }
    }

    public function validateToken(IObject $object)
    {
        if ($object->token !== Core::app()->request->getToken()) {
            throw self::exceptionSessionInvalidToken('Invalid session token <{token}>', ['token' => Core::app()->request->getToken()]);
        }
    }
}