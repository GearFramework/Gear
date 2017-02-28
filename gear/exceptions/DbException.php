<?php

use gear\library\GException;

class DatabaseException extends GException {}
class DatabaseConnectionException extends  DatabaseException {}
class DbCursorException extends DatabaseException {}