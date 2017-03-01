<?php

class SessionException extends \gear\library\GException {}
class SessionExpiredException extends SessionException {}
class SessionInvalidTokenException extends SessionException {}