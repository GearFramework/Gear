<?php

class UserException extends \gear\library\GException {}
class UserInvalidIdentityException extends UserException {
    protected $_defaultMessage = 'Invalid user identity';
}